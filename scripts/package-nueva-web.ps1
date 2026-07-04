param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..").Path,
    [string]$OutputRoot = "",
    [string]$ZipPath = ""
)

$ErrorActionPreference = "Stop"

$ProjectRoot = (Resolve-Path $ProjectRoot).Path
if ([string]::IsNullOrWhiteSpace($OutputRoot)) {
    $OutputRoot = Join-Path $ProjectRoot "dist\nueva_web"
}
if ([string]::IsNullOrWhiteSpace($ZipPath)) {
    $ZipPath = Join-Path $ProjectRoot "dist\nueva_web.zip"
}

powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path $ProjectRoot "scripts\build-nueva-web.ps1") -ProjectRoot $ProjectRoot -OutputRoot $OutputRoot
powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path $ProjectRoot "scripts\validate-staging.ps1") -ProjectRoot $ProjectRoot -OutputRoot $OutputRoot

$OutputRoot = (Resolve-Path $OutputRoot).Path
$ZipParent = Split-Path -Parent $ZipPath
New-Item -ItemType Directory -Force -Path $ZipParent | Out-Null
$ZipParent = (Resolve-Path $ZipParent).Path
$ZipPath = Join-Path $ZipParent (Split-Path -Leaf $ZipPath)

$secretPath = Join-Path $OutputRoot "api\.env"
if (Test-Path $secretPath) {
    throw "Refusing to package because api\.env is present in staging: $secretPath"
}

if (Test-Path $ZipPath) {
    Remove-Item -LiteralPath $ZipPath -Force
}

$children = Join-Path $OutputRoot "*"
Compress-Archive -Path $children -DestinationPath $ZipPath -Force

Add-Type -AssemblyName System.IO.Compression.FileSystem
$archive = [System.IO.Compression.ZipFile]::OpenRead($ZipPath)
try {
    $entryNames = $archive.Entries | ForEach-Object { $_.FullName -replace "\\", "/" }
    foreach ($required in @("index.html", ".htaccess", "README_DEPLOY.txt", "api/public/index.php", "api/.env.example", "api/vendor/autoload.php", "build-manifest.json")) {
        if ($entryNames -notcontains $required) {
            throw "Zip package is missing required entry: $required"
        }
    }
    if ($entryNames -contains "api/.env") {
        throw "Zip package includes forbidden api\.env"
    }
} finally {
    $archive.Dispose()
}

Write-Host "Package ready: $ZipPath"
