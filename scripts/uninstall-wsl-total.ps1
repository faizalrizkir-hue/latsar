param(
    [switch]$RunElevated
)

$ErrorActionPreference = 'Continue'
$ScriptPath = $MyInvocation.MyCommand.Path
$RepoRoot = Split-Path -Parent (Split-Path -Parent $ScriptPath)
$LogFile = Join-Path $RepoRoot 'backups\wsl-uninstall-log.txt'

function Write-Log {
    param([string]$Message)
    $line = "[{0}] {1}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $Message
    Write-Output $line
    Add-Content -Path $LogFile -Value $line
}

function Test-Admin {
    $id = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = [Security.Principal.WindowsPrincipal]::new($id)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

New-Item -ItemType Directory -Force -Path (Split-Path -Parent $LogFile) | Out-Null
Set-Content -Path $LogFile -Value '' -Encoding UTF8
Write-Log 'Mulai uninstall total WSL.'

if (-not (Test-Admin)) {
    Write-Log 'Tidak berjalan sebagai Administrator. Meminta elevasi UAC...'
    $args = "-NoProfile -ExecutionPolicy Bypass -File `"$ScriptPath`" -RunElevated"
    Start-Process -FilePath 'powershell.exe' -Verb RunAs -ArgumentList $args
    Write-Log 'Jendela elevasi dibuka. Klik Yes pada UAC untuk melanjutkan.'
    exit 0
}

Write-Log 'Berjalan sebagai Administrator.'

try {
    $distros = @()
    $raw = & wsl.exe --list --quiet 2>$null
    if ($raw) {
        $distros = $raw |
            ForEach-Object { ($_ -replace "`0", '').Trim() } |
            Where-Object { $_ -ne '' }
    }

    if ($distros.Count -eq 0) {
        Write-Log 'Tidak ada distro WSL yang terdaftar.'
    } else {
        foreach ($distro in $distros) {
            Write-Log ("Terminate distro: {0}" -f $distro)
            & wsl.exe --terminate $distro 2>$null | Out-Null
            Write-Log ("Unregister distro: {0}" -f $distro)
            & wsl.exe --unregister $distro 2>&1 | ForEach-Object { Write-Log $_ }
        }
    }

    Write-Log 'Shutdown semua instance WSL.'
    & wsl.exe --shutdown 2>$null | Out-Null

    Write-Log 'Disable Windows feature: Microsoft-Windows-Subsystem-Linux'
    & dism.exe /online /disable-feature /featurename:Microsoft-Windows-Subsystem-Linux /NoRestart 2>&1 | ForEach-Object { Write-Log $_ }

    Write-Log 'Disable Windows feature: VirtualMachinePlatform'
    & dism.exe /online /disable-feature /featurename:VirtualMachinePlatform /NoRestart 2>&1 | ForEach-Object { Write-Log $_ }

    Write-Log 'Hapus paket appx WSL (jika ada).'
    $packages = Get-AppxPackage -AllUsers *WindowsSubsystemForLinux*
    if ($packages) {
        foreach ($pkg in $packages) {
            Write-Log ("Remove Appx: {0}" -f $pkg.PackageFullName)
            try {
                Remove-AppxPackage -Package $pkg.PackageFullName -AllUsers -ErrorAction Stop
            } catch {
                Write-Log ("Gagal Remove-AppxPackage: {0}" -f $_.Exception.Message)
            }
        }
    } else {
        Write-Log 'Tidak ada paket appx WSL terdeteksi.'
    }

    Write-Log 'Selesai uninstall total WSL. Disarankan restart Windows.'
    Write-Output ''
    Write-Output 'WSL berhasil dihapus. Silakan restart Windows.'
    Write-Output ("Log: {0}" -f $LogFile)
} catch {
    Write-Log ("ERROR: {0}" -f $_.Exception.Message)
    Write-Output ''
    Write-Output ("Gagal: {0}" -f $_.Exception.Message)
    Write-Output ("Log: {0}" -f $LogFile)
    exit 1
}
