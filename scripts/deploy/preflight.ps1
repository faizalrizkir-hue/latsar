param(
    [string]$EnvFile = ".env",
    [switch]$SkipRealtime
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
    param(
        [string]$Key
    )

    if ($envMap.ContainsKey($Key)) {
        return [string]$envMap[$Key]
    }

    return ""
}

$errors = 0
$warnings = 0

function Add-ErrorMessage {
    param(
        [string]$Message
    )

    Write-Host "ERROR: $Message" -ForegroundColor Red
    $script:errors++
}

function Add-WarningMessage {
    param(
        [string]$Message
    )

    Write-Host "WARN: $Message" -ForegroundColor Yellow
    $script:warnings++
}

function Require-NotEmpty {
    param(
        [string]$Key
    )

    $value = Get-EnvValue -Key $Key
    if ([string]::IsNullOrWhiteSpace($value)) {
        Add-ErrorMessage "$Key kosong atau belum diisi."
    }
}

function Require-Equals {
    param(
        [string]$Key,
        [string]$Expected
    )

    $value = Get-EnvValue -Key $Key
    if ($value -ne $Expected) {
        Add-ErrorMessage "$Key harus '$Expected', nilai saat ini: '$value'."
    }
}

function Warn-IfNotEquals {
    param(
        [string]$Key,
        [string]$Expected
    )

    $value = Get-EnvValue -Key $Key
    if ($value -ne $Expected) {
        Add-WarningMessage "$Key disarankan '$Expected', nilai saat ini: '$value'."
    }
}

Write-Host "== LATSAR production preflight =="
Write-Host "ENV file: $EnvFile"
if ($SkipRealtime.IsPresent) {
    Write-Host "Mode: realtime optional (-SkipRealtime)"
} else {
    Write-Host "Mode: realtime required"
}

Require-Equals -Key "APP_ENV" -Expected "production"
Require-Equals -Key "APP_DEBUG" -Expected "false"
Require-NotEmpty -Key "APP_KEY"
Require-NotEmpty -Key "APP_URL"
Require-NotEmpty -Key "DB_CONNECTION"
Require-NotEmpty -Key "DB_HOST"
Require-NotEmpty -Key "DB_PORT"
Require-NotEmpty -Key "DB_DATABASE"
Require-NotEmpty -Key "DB_USERNAME"

if (-not $SkipRealtime.IsPresent) {
    Require-NotEmpty -Key "REVERB_APP_ID"
    Require-NotEmpty -Key "REVERB_APP_KEY"
    Require-NotEmpty -Key "REVERB_APP_SECRET"
    Require-NotEmpty -Key "REVERB_HOST"
    Require-NotEmpty -Key "REVERB_PORT"
    Require-NotEmpty -Key "REVERB_SCHEME"
}

$appUrl = Get-EnvValue -Key "APP_URL"
if (-not [string]::IsNullOrWhiteSpace($appUrl) -and -not $appUrl.StartsWith("https://")) {
    Add-ErrorMessage "APP_URL harus menggunakan https://"
}

Warn-IfNotEquals -Key "SESSION_SECURE_COOKIE" -Expected "true"
Warn-IfNotEquals -Key "SECURITY_HEADERS_ENABLED" -Expected "true"
Warn-IfNotEquals -Key "SECURITY_HEADERS_HSTS_ENABLED" -Expected "true"

$assetVersion = Get-EnvValue -Key "ASSET_VERSION"
if ([string]::IsNullOrWhiteSpace($assetVersion)) {
    Add-WarningMessage "ASSET_VERSION kosong; disarankan isi versi rilis untuk cache busting saat deploy."
}

if (-not $SkipRealtime.IsPresent) {
    Warn-IfNotEquals -Key "BROADCAST_CONNECTION" -Expected "reverb"
} else {
    $broadcastConnection = Get-EnvValue -Key "BROADCAST_CONNECTION"
    if ($broadcastConnection -eq "reverb") {
        Add-WarningMessage "mode -SkipRealtime aktif tetapi BROADCAST_CONNECTION masih 'reverb'."
    }
}

Write-Host ""
if ($errors -gt 0) {
    Write-Host "Preflight GAGAL: $errors error, $warnings warning." -ForegroundColor Red
    exit 1
}

Write-Host "Preflight OK: 0 error, $warnings warning." -ForegroundColor Green
exit 0
