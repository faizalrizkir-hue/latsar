param(
    [Parameter(Mandatory = $true)]
    [string]$BackupDir
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path -LiteralPath $BackupDir)) {
    Write-Host "ERROR: backup directory tidak ditemukan: $BackupDir" -ForegroundColor Red
    exit 1
}

$resolvedBackupDir = (Resolve-Path -LiteralPath $BackupDir).Path
$dbDumpPath = Join-Path $resolvedBackupDir "db.sql"
$uploadsZipPath = Join-Path $resolvedBackupDir "uploads.zip"
$manifestPath = Join-Path $resolvedBackupDir "manifest.json"

if (-not (Test-Path -LiteralPath $dbDumpPath)) {
    Write-Host "ERROR: file db.sql tidak ditemukan." -ForegroundColor Red
    exit 1
}

$dbSize = (Get-Item -LiteralPath $dbDumpPath).Length
if ($dbSize -le 0) {
    Write-Host "ERROR: file db.sql kosong." -ForegroundColor Red
    exit 1
}

if (Test-Path -LiteralPath $uploadsZipPath) {
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    $zip = [System.IO.Compression.ZipFile]::OpenRead($uploadsZipPath)
    try {
        if ($zip.Entries.Count -le 0) {
            Write-Host "ERROR: uploads.zip tidak berisi file." -ForegroundColor Red
            exit 1
        }
    } finally {
        $zip.Dispose()
    }
}

if (Test-Path -LiteralPath $manifestPath) {
    $manifest = Get-Content -Raw -LiteralPath $manifestPath | ConvertFrom-Json
    foreach ($item in $manifest.files) {
        $target = Join-Path $resolvedBackupDir $item.name
        if (-not (Test-Path -LiteralPath $target)) {
            Write-Host "ERROR: file di manifest tidak ditemukan: $($item.name)" -ForegroundColor Red
            exit 1
        }
        $actualHash = (Get-FileHash -LiteralPath $target -Algorithm SHA256).Hash
        if (-not [string]::Equals($actualHash, [string]$item.sha256, [System.StringComparison]::OrdinalIgnoreCase)) {
            Write-Host "ERROR: hash tidak cocok untuk $($item.name)" -ForegroundColor Red
            exit 1
        }
    }
}

Write-Host "Backup valid: $resolvedBackupDir" -ForegroundColor Green
exit 0
