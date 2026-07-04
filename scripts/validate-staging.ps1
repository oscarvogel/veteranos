param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..").Path,
    [string]$OutputRoot = ""
)

$ErrorActionPreference = "Stop"

$ProjectRoot = (Resolve-Path $ProjectRoot).Path
if ([string]::IsNullOrWhiteSpace($OutputRoot)) {
    $OutputRoot = Join-Path $ProjectRoot "dist\nueva_web"
}
$OutputRoot = (Resolve-Path $OutputRoot).Path

function Assert-Exists {
    param([string]$Path)
    if (-not (Test-Path $Path)) {
        throw "Missing required staging path: $Path"
    }
}

function Assert-NotExists {
    param([string]$Path)
    if (Test-Path $Path) {
        throw "Unexpected staging path present: $Path"
    }
}

$manifestPath = Join-Path $OutputRoot "build-manifest.json"
$htaccessPath = Join-Path $OutputRoot ".htaccess"
$apiRoot = Join-Path $OutputRoot "api"

Assert-Exists $manifestPath
Assert-Exists $htaccessPath
Assert-Exists (Join-Path $OutputRoot "index.html")
Assert-Exists (Join-Path $OutputRoot "README_DEPLOY.txt")
Assert-Exists (Join-Path $apiRoot "public\index.php")
Assert-Exists (Join-Path $apiRoot "app\lib\env_loader.php")
Assert-Exists (Join-Path $apiRoot "vendor\autoload.php")
Assert-Exists (Join-Path $apiRoot "composer.json")
Assert-Exists (Join-Path $apiRoot "composer.lock")
Assert-Exists (Join-Path $apiRoot ".env.example")

# The real secret file must be created on the server, never copied to staging.
Assert-NotExists (Join-Path $apiRoot ".env")

$manifest = Get-Content $manifestPath -Raw | ConvertFrom-Json
if ($manifest.copied_env -ne $false) {
    throw "build-manifest.json reports copied_env=$($manifest.copied_env)"
}
if ([int]$manifest.frontend_files -lt 3) {
    throw "Unexpectedly low frontend file count in manifest: $($manifest.frontend_files)"
}
if ([int]$manifest.api_files -lt 100) {
    throw "Unexpectedly low API file count in manifest: $($manifest.api_files)"
}

$htaccess = Get-Content $htaccessPath -Raw
if ($htaccess -notmatch "RewriteBase /nueva_web/" -or $htaccess -notmatch "api/public/index.php") {
    throw ".htaccess does not include expected /nueva_web API rewrite rules"
}

$deployReadme = Get-Content (Join-Path $OutputRoot "README_DEPLOY.txt") -Raw
if ($deployReadme -notmatch "api/.env" -or $deployReadme -notmatch "API_DEBUG=false") {
    throw "README_DEPLOY.txt does not include expected environment setup notes"
}

$bundleMatches = Select-String -Path (Join-Path $OutputRoot "assets\*.js") -Pattern "https://veteranos.ar/nueva_web" -SimpleMatch
if (-not $bundleMatches) {
    throw "Production API base https://veteranos.ar/nueva_web was not found in frontend bundle"
}

Write-Host "Staging validation passed for $OutputRoot"
