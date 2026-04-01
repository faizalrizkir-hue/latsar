param(
    [int]$Port = 3306,
    [string]$LaragonPathMarker = '\laragon\bin\mysql\',
    [ValidateSet('Manual', 'Disabled')]
    [string]$NonLaragonStartupType = 'Manual'
)

$ErrorActionPreference = 'Stop'

function Assert-Admin {
    $currentIdentity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = [Security.Principal.WindowsPrincipal]::new($currentIdentity)
    $isAdmin = $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
    if (-not $isAdmin) {
        throw 'Script harus dijalankan sebagai Administrator (Run as Administrator).'
    }
}

function Get-ListenPids([int]$TargetPort) {
    $listeners = Get-NetTCPConnection -State Listen -LocalPort $TargetPort -ErrorAction SilentlyContinue
    if (-not $listeners) {
        return @()
    }

    return $listeners |
        Select-Object -ExpandProperty OwningProcess -Unique |
        Where-Object { $_ -gt 0 }
}

function Get-ProcessServiceMap([int[]]$Pids, [string]$Marker) {
    $serviceList = Get-CimInstance Win32_Service
    $result = @()

    foreach ($pid in $Pids) {
        $proc = Get-CimInstance Win32_Process -Filter "ProcessId=$pid" -ErrorAction SilentlyContinue
        if (-not $proc) {
            continue
        }

        $exePath = ''
        if ($null -ne $proc.ExecutablePath) {
            $exePath = [string]$proc.ExecutablePath
        }

        $cmdLine = ''
        if ($null -ne $proc.CommandLine) {
            $cmdLine = [string]$proc.CommandLine
        }
        $boundServices = $serviceList | Where-Object { $_.ProcessId -eq $pid }
        $serviceNames = @($boundServices | Select-Object -ExpandProperty Name)
        $serviceDisplayNames = @($boundServices | Select-Object -ExpandProperty DisplayName)

        $isLaragon = $false
        if ($exePath -ne '') {
            $isLaragon = $exePath.ToLowerInvariant().Contains($Marker.ToLowerInvariant())
        }

        $result += [PSCustomObject]@{
            PID              = $pid
            ExecutablePath   = $exePath
            CommandLine      = $cmdLine
            ServiceNames     = ($serviceNames -join ', ')
            ServiceDisplays  = ($serviceDisplayNames -join ', ')
            IsLaragon        = $isLaragon
        }
    }

    return $result
}

function Stop-NonLaragonEngines($ProcessMap, [string]$StartupType) {
    $nonLaragon = $ProcessMap | Where-Object { -not $_.IsLaragon }
    if (-not $nonLaragon) {
        Write-Host '[OK] Tidak ada proses MySQL non-Laragon yang perlu dihentikan.' -ForegroundColor Green
        return
    }

    foreach ($item in $nonLaragon) {
        $resolvedPath = 'unknown'
        if ($item.ExecutablePath -ne '') {
            $resolvedPath = $item.ExecutablePath
        }

        Write-Host ("[INFO] Menangani PID {0} ({1})" -f $item.PID, $resolvedPath) -ForegroundColor Yellow

        $serviceNames = @()
        if ($item.ServiceNames -ne '') {
            $serviceNames = $item.ServiceNames -split '\s*,\s*' | Where-Object { $_ -ne '' }
        }

        foreach ($svc in $serviceNames) {
            try {
                Write-Host ("  - Stop-Service {0}" -f $svc) -ForegroundColor Yellow
                Stop-Service -Name $svc -Force -ErrorAction Stop
            } catch {
                Write-Host ("  - Skip Stop-Service {0}: {1}" -f $svc, $_.Exception.Message) -ForegroundColor DarkYellow
            }

            try {
                Write-Host ("  - Set-Service {0} StartupType {1}" -f $svc, $StartupType) -ForegroundColor Yellow
                Set-Service -Name $svc -StartupType $StartupType -ErrorAction Stop
            } catch {
                Write-Host ("  - Skip Set-Service {0}: {1}" -f $svc, $_.Exception.Message) -ForegroundColor DarkYellow
            }
        }

        try {
            Write-Host ("  - Stop-Process PID {0}" -f $item.PID) -ForegroundColor Yellow
            Stop-Process -Id $item.PID -Force -ErrorAction Stop
        } catch {
            Write-Host ("  - Skip Stop-Process {0}: {1}" -f $item.PID, $_.Exception.Message) -ForegroundColor DarkYellow
        }
    }
}

try {
    Assert-Admin

    Write-Host ("[STEP] Cek listener port {0}..." -f $Port) -ForegroundColor Cyan
    $initialPids = Get-ListenPids -TargetPort $Port
    if (-not $initialPids) {
        Write-Host ("[INFO] Tidak ada listener di port {0}." -f $Port) -ForegroundColor Yellow
        exit 0
    }

    $processMap = Get-ProcessServiceMap -Pids $initialPids -Marker $LaragonPathMarker
    Write-Host '[INFO] Daftar proses DB yang aktif:' -ForegroundColor Cyan
    $processMap | Format-Table PID, IsLaragon, ExecutablePath, ServiceNames -AutoSize

    $laragonCount = @($processMap | Where-Object { $_.IsLaragon }).Count
    if ($laragonCount -eq 0) {
        Write-Host '[PERINGATAN] Tidak terdeteksi mysqld Laragon di port ini. Script tidak akan mematikan proses untuk mencegah salah target.' -ForegroundColor Red
        exit 2
    }

    Stop-NonLaragonEngines -ProcessMap $processMap -StartupType $NonLaragonStartupType

    Start-Sleep -Seconds 1
    Write-Host ("[STEP] Verifikasi ulang listener port {0}..." -f $Port) -ForegroundColor Cyan
    $finalPids = Get-ListenPids -TargetPort $Port
    $finalMap = Get-ProcessServiceMap -Pids $finalPids -Marker $LaragonPathMarker
    $finalMap | Format-Table PID, IsLaragon, ExecutablePath, ServiceNames -AutoSize

    $nonLaragonRemain = @($finalMap | Where-Object { -not $_.IsLaragon }).Count
    if ($nonLaragonRemain -gt 0) {
        Write-Host '[WARNING] Masih ada proses non-Laragon di port 3306. Ulangi script atau cek service manual.' -ForegroundColor Red
        exit 3
    }

    Write-Host '[OK] Konflik DB 3306 sudah dibersihkan. Silakan reload aplikasi.' -ForegroundColor Green
    exit 0
} catch {
    Write-Host ("[ERROR] {0}" -f $_.Exception.Message) -ForegroundColor Red
    exit 1
}
