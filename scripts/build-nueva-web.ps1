param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..").Path,
    [string]$FrontendRoot = "",
    [string]$OutputRoot = ""
)

$ErrorActionPreference = "Stop"

if ([string]::IsNullOrWhiteSpace($OutputRoot)) {
    $OutputRoot = Join-Path $ProjectRoot "dist\nueva_web"
}

$ProjectRoot = (Resolve-Path $ProjectRoot).Path
if ([string]::IsNullOrWhiteSpace($FrontendRoot)) {
    $FrontendRoot = Join-Path $ProjectRoot "frontend"
}
$FrontendRoot = (Resolve-Path $FrontendRoot).Path
$OutputParent = Split-Path -Parent $OutputRoot
New-Item -ItemType Directory -Force -Path $OutputParent | Out-Null
$OutputParent = (Resolve-Path $OutputParent).Path

$resolvedOutput = if (Test-Path $OutputRoot) {
    (Resolve-Path $OutputRoot).Path
} else {
    $OutputRoot
}

$expectedRoot = Join-Path $ProjectRoot "dist"
if (-not $resolvedOutput.StartsWith($expectedRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw "OutputRoot must stay inside $expectedRoot. Received: $resolvedOutput"
}

Write-Host "Building Vue frontend..."
Push-Location $FrontendRoot
try {
    npm run build
} finally {
    Pop-Location
}

if (Test-Path $OutputRoot) {
    Remove-Item -LiteralPath $OutputRoot -Recurse -Force
}

New-Item -ItemType Directory -Force -Path $OutputRoot | Out-Null

Write-Host "Copying frontend dist..."
Copy-Item -Path (Join-Path $FrontendRoot "dist\*") -Destination $OutputRoot -Recurse -Force
Copy-Item -Path (Join-Path $ProjectRoot "docs\htaccess-nueva-web.txt") -Destination (Join-Path $OutputRoot ".htaccess") -Force
Copy-Item -Path (Join-Path $ProjectRoot "docs\README_DEPLOY_nueva_web.txt") -Destination (Join-Path $OutputRoot "README_DEPLOY.txt") -Force

$apiSource = Join-Path $ProjectRoot "api"
$apiTarget = Join-Path $OutputRoot "api"
New-Item -ItemType Directory -Force -Path $apiTarget | Out-Null

Write-Host "Copying API..."
foreach ($dir in @("app", "public", "src", "templates", "vendor")) {
    Copy-Item -Path (Join-Path $apiSource $dir) -Destination (Join-Path $apiTarget $dir) -Recurse -Force
}

foreach ($file in @("composer.json", "composer.lock", ".env.example")) {
    Copy-Item -Path (Join-Path $apiSource $file) -Destination (Join-Path $apiTarget $file) -Force
}

New-Item -ItemType Directory -Force -Path (Join-Path $apiTarget "logs") | Out-Null
if (Test-Path (Join-Path $apiSource "logs\README.md")) {
    Copy-Item -Path (Join-Path $apiSource "logs\README.md") -Destination (Join-Path $apiTarget "logs\README.md") -Force
}

$manifest = [ordered]@{
    generated_at = (Get-Date).ToString("s")
    project_root = $ProjectRoot
    frontend_root = $FrontendRoot
    output_root = (Resolve-Path $OutputRoot).Path
    frontend_files = (Get-ChildItem -Path $OutputRoot -File -Recurse | Where-Object { $_.FullName -notlike "*\api\*" }).Count
    api_files = (Get-ChildItem -Path $apiTarget -File -Recurse).Count
    copied_env = $false
}

$manifest | ConvertTo-Json -Depth 3 | Set-Content -Path (Join-Path $OutputRoot "build-manifest.json") -Encoding ASCII

Write-Host "Staging ready: $OutputRoot"
Write-Host "Remember to create api\.env on the server from api\.env.example."
