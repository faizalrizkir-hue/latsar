param(
    [string]$PhpBin = "php",
    [switch]$ClearFirst
)

$ErrorActionPreference = "Stop"

Write-Host "== LATSAR runtime optimize =="
Write-Host "PHP_BIN: $PhpBin"

if ($ClearFirst.IsPresent) {
    Write-Host "[1/3] php artisan optimize:clear"
    & $PhpBin artisan optimize:clear
} else {
    Write-Host "[1/2] skip optimize:clear"
}

if ($ClearFirst.IsPresent) {
    Write-Host "[2/3] php artisan optimize"
} else {
    Write-Host "[2/2] php artisan optimize"
}
& $PhpBin artisan optimize

Write-Host "php artisan queue:restart (best effort)"
try {
    & $PhpBin artisan queue:restart
} catch {
    Write-Warning "queue:restart gagal (queue worker mungkin belum aktif)."
}

Write-Host "Selesai."
