param(
    [string]$EnvFile = ".env",
    [string]$OutputDir = "backups/runtime",
    [string]$MysqldumpBin = "mysqldump"
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path -LiteralPath $EnvFile)) {
    Write-Host "ERROR: file '$EnvFile' tidak ditemukan." -ForegroundColor Red
    exit 1
}

$lines = Get-Content -LiteralPath $EnvFile
$envMap = @{}

foreach ($rawLine in $lines) {
    $line = $rawLine.Trim()
    if ($line -eq "" -or $line.StartsWith("#")) {
        continue
    }

    $idx = $line.IndexOf("=")
    if ($idx -lt 1) {
        continue
    }

    $key = $line.Substring(0, $idx).Trim()
    $value = $line.Substring($idx + 1).Trim()
    if ($value.StartsWith('"') -and $value.EndsWith('"') -and $value.Length -ge 2) {
        $value = $value.Substring(1, $value.Length - 2)
    }

    $envMap[$key] = $value
}

function Get-EnvValue {
    param([string]$Key)
    if ($envMap.ContainsKey($Key)) {
        return [string]$envMap[$Key]
    }
    return ""
}

$dbHost = Get-EnvValue -Key "DB_HOST"
$dbPort = Get-EnvValue -Key "DB_PORT"
$dbName = Get-EnvValue -Key "DB_DATABASE"
$dbUser = Get-EnvValue -Key "DB_USERNAME"
$dbPass = Get-EnvValue -Key "DB_PASSWORD"

if ([string]::IsNullOrWhiteSpace($dbHost) -or [string]::IsNullOrWhiteSpace($dbPort) -or [string]::IsNullOrWhiteSpace($dbName) -or [string]::IsNullOrWhiteSpace($dbUser)) {
    Write-Host "ERROR: konfigurasi DB di env belum lengkap (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME)." -ForegroundColor Red
    exit 1
}

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupRoot = Join-Path (Get-Location) $OutputDir
$backupDir = Join-Path $backupRoot $timestamp
$dbDumpPath = Join-Path $backupDir "db.sql"
$dbErrPath = Join-Path $backupDir "db.stderr.log"
$uploadsZipPath = Join-Path $backupDir "uploads.zip"
$manifestPath = Join-Path $backupDir "manifest.json"

New-Item -ItemType Directory -Force -Path $backupDir | Out-Null

Write-Host "== LATSAR Backup =="
Write-Host "Backup dir: $backupDir"

$dumpArgs = @(
    "--host=$dbHost",
    "--port=$dbPort",
    "--user=$dbUser",
    "--single-transaction",
    "--quick",
    "--skip-lock-tables",
    "--default-character-set=utf8mb4",
    $dbName
)

if (-not [string]::IsNullOrWhiteSpace($dbPass)) {
    $dumpArgs = @("--password=$dbPass") + $dumpArgs
}

$dumpProcess = Start-Process -FilePath $MysqldumpBin `
    -ArgumentList $dumpArgs `
    -NoNewWindow `
    -Wait `
    -PassThru `
    -RedirectStandardOutput $dbDumpPath `
    -RedirectStandardError $dbErrPath

if ($dumpProcess.ExitCode -ne 0) {
    Write-Host "ERROR: mysqldump gagal (exit code $($dumpProcess.ExitCode)). Cek: $dbErrPath" -ForegroundColor Red
    exit 1
}

$dbFileInfo = Get-Item -LiteralPath $dbDumpPath
if ($dbFileInfo.Length -le 0) {
    Write-Host "ERROR: file dump database kosong: $dbDumpPath" -ForegroundColor Red
    exit 1
}

$uploadsPath = Join-Path (Get-Location) "public/uploads"
$uploadsArchived = $false
if (Test-Path -LiteralPath $uploadsPath) {
    $uploadsItems = Get-ChildItem -LiteralPath $uploadsPath -Force
    if ($uploadsItems.Count -gt 0) {
        Compress-Archive -Path (Join-Path $uploadsPath "*") -DestinationPath $uploadsZipPath -Force
        $uploadsArchived = $true
    }
}

$files = @($dbDumpPath)
if ($uploadsArchived) {
    $files += $uploadsZipPath
}

$manifest = [ordered]@{
    created_at = (Get-Date).ToString("o")
    env_file = (Resolve-Path -LiteralPath $EnvFile).Path
    db = [ordered]@{
        host = $dbHost
        port = $dbPort
        database = $dbName
        username = $dbUser
    }
    files = @()
}

foreach ($filePath in $files) {
    $fileInfo = Get-Item -LiteralPath $filePath
    $fileHash = (Get-FileHash -LiteralPath $filePath -Algorithm SHA256).Hash
    $manifest.files += [ordered]@{
        name = $fileInfo.Name
        size_bytes = $fileInfo.Length
        sha256 = $fileHash
    }
}

$manifestJson = $manifest | ConvertTo-Json -Depth 5
[System.IO.File]::WriteAllText($manifestPath, $manifestJson, (New-Object System.Text.UTF8Encoding($false)))

Write-Host "Backup database: $dbDumpPath"
if ($uploadsArchived) {
    Write-Host "Backup uploads : $uploadsZipPath"
} else {
    Write-Warning "Folder public/uploads kosong atau tidak ada, arsip upload tidak dibuat."
}
Write-Host "Manifest       : $manifestPath"
Write-Host "Selesai."
