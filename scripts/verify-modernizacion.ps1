param(
    [string]$ProjectRoot = (Resolve-Path "$PSScriptRoot\..").Path,
    [string]$BaseUrl = "http://127.0.0.1:8017/nueva_web",
    [switch]$RequireDatabase
)

$ErrorActionPreference = "Stop"

$ProjectRoot = (Resolve-Path $ProjectRoot).Path
$FrontendRoot = Join-Path $ProjectRoot "frontend"

function Invoke-Step {
    param(
        [string]$Name,
        [scriptblock]$Command
    )

    Write-Host ""
    Write-Host "== $Name =="
    & $Command
}

Invoke-Step "PHP unit checks" {
    Push-Location $ProjectRoot
    try {
        php api\tests\modern_api_response_test.php
        php api\tests\env_loader_test.php
        php api\tests\request_path_test.php
        php api\tests\settings_test.php
        php api\tests\package_script_test.php
        php api\tests\staging_script_test.php
        php api\tests\verify_script_test.php
    } finally {
        Pop-Location
    }
}

Invoke-Step "PHP HTTP health checks" {
    Push-Location $ProjectRoot
    try {
        php api\tests\health_http_test.php
    } finally {
        Pop-Location
    }
}

Invoke-Step "Frontend tests and audit" {
    Push-Location $FrontendRoot
    try {
        npm test
        npm audit
    } finally {
        Pop-Location
    }
}

Invoke-Step "Build staging package" {
    Push-Location $ProjectRoot
    try {
        powershell -NoProfile -ExecutionPolicy Bypass -File scripts\build-nueva-web.ps1
        powershell -NoProfile -ExecutionPolicy Bypass -File scripts\validate-staging.ps1
    } finally {
        Pop-Location
    }
}

Invoke-Step "Smoke test staging API" {
    Push-Location $ProjectRoot
    try {
        if ($RequireDatabase) {
            powershell -NoProfile -ExecutionPolicy Bypass -File scripts\test-nueva-web.ps1 -BaseUrl $BaseUrl -RequireDatabase
        } else {
            powershell -NoProfile -ExecutionPolicy Bypass -File scripts\test-nueva-web.ps1 -BaseUrl $BaseUrl -CheckDatabase
        }
    } finally {
        Pop-Location
    }
}

Write-Host ""
Write-Host "Modernizacion verification passed for $ProjectRoot"
