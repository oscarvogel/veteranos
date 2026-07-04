param(
    [string]$BaseUrl = "http://127.0.0.1:8017/nueva_web",
    [switch]$CheckDatabase,
    [switch]$RequireDatabase
)

$ErrorActionPreference = "Stop"

function Convert-JsonContent {
    param(
        [string]$Content
    )

    return $Content | ConvertFrom-Json
}

function Invoke-JsonCheck {
    param(
        [string]$Path,
        [int]$ExpectedStatus,
        [scriptblock]$Assert
    )

    $url = $BaseUrl.TrimEnd("/") + $Path
    try {
        $request = [System.Net.WebRequest]::Create($url)
        $request.Timeout = 15000
        $response = $request.GetResponse()
        $statusCode = [int]$response.StatusCode
        $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
        $content = $reader.ReadToEnd()
    } catch {
        $webException = $_.Exception
        if ($webException.InnerException -and $webException.InnerException.Response) {
            $webException = $webException.InnerException
        }

        if (-not $webException.Response) {
            throw
        }

        $statusCode = [int]$webException.Response.StatusCode
        $reader = New-Object System.IO.StreamReader($webException.Response.GetResponseStream())
        $content = $reader.ReadToEnd()
    }

    if ($statusCode -ne $ExpectedStatus) {
        throw "Expected HTTP $ExpectedStatus for $url, got $statusCode`: $content"
    }

    $json = Convert-JsonContent -Content $content
    & $Assert $json $url
    Write-Host "OK $statusCode $Path"
}

function Invoke-ContentCheck {
    param(
        [string]$Path,
        [int]$ExpectedStatus,
        [scriptblock]$Assert
    )

    $url = $BaseUrl.TrimEnd("/") + $Path
    try {
        $request = [System.Net.WebRequest]::Create($url)
        $request.Timeout = 15000
        $response = $request.GetResponse()
        $statusCode = [int]$response.StatusCode
        $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
        $content = $reader.ReadToEnd()
    } catch {
        $webException = $_.Exception
        if ($webException.InnerException -and $webException.InnerException.Response) {
            $webException = $webException.InnerException
        }

        if (-not $webException.Response) {
            throw
        }

        $statusCode = [int]$webException.Response.StatusCode
        $reader = New-Object System.IO.StreamReader($webException.Response.GetResponseStream())
        $content = $reader.ReadToEnd()
    }

    if ($statusCode -ne $ExpectedStatus) {
        throw "Expected HTTP $ExpectedStatus for $url, got $statusCode`: $content"
    }

    & $Assert $content $url
    Write-Host "OK $statusCode $Path"
}

function Invoke-JsonProbe {
    param(
        [string]$Path
    )

    $url = $BaseUrl.TrimEnd("/") + $Path
    try {
        $request = [System.Net.WebRequest]::Create($url)
        $request.Timeout = 15000
        $response = $request.GetResponse()
        $statusCode = [int]$response.StatusCode
        $reader = New-Object System.IO.StreamReader($response.GetResponseStream())
        $content = $reader.ReadToEnd()
    } catch {
        $webException = $_.Exception
        if ($webException.InnerException -and $webException.InnerException.Response) {
            $webException = $webException.InnerException
        }

        if (-not $webException.Response) {
            throw
        }

        $statusCode = [int]$webException.Response.StatusCode
        $reader = New-Object System.IO.StreamReader($webException.Response.GetResponseStream())
        $content = $reader.ReadToEnd()
    }

    return [pscustomobject]@{
        Url = $url
        StatusCode = $statusCode
        Json = (Convert-JsonContent -Content $content)
        Content = $content
    }
}

function Assert-NoSensitiveDatabaseDetail {
    param(
        $Json,
        [string]$Url
    )

    if ($Json.error.message -match "SQLSTATE|Access denied|ye000174|localhost|veteranos") {
        throw "Sensitive database detail leaked from $url`: $($Json.error.message)"
    }
}

Invoke-JsonCheck -Path "/api/health" -ExpectedStatus 200 -Assert {
    param($json, $url)
    if ($json.success -ne $true -or $json.data.status -ne "ok") {
        throw "Invalid health payload from $url"
    }
}

Invoke-JsonCheck -Path "/api/fixture" -ExpectedStatus 400 -Assert {
    param($json, $url)
    if ($json.success -ne $false -or [int]$json.error.code -ne 400) {
        throw "Invalid validation payload from $url"
    }
}

$fixtureProbe = Invoke-JsonProbe -Path "/api/fixture?torneo_id=1"
if ($fixtureProbe.StatusCode -eq 200) {
    if ($fixtureProbe.Json.success -ne $true -or $null -eq $fixtureProbe.Json.data) {
        throw "Invalid fixture success payload from $($fixtureProbe.Url)"
    }
    Write-Host "OK 200 /api/fixture?torneo_id=1"
} elseif ($fixtureProbe.StatusCode -eq 500 -and -not $RequireDatabase) {
    if ($fixtureProbe.Json.success -ne $false -or [int]$fixtureProbe.Json.error.code -ne 500) {
        throw "Invalid database-error payload from $($fixtureProbe.Url)"
    }
    Assert-NoSensitiveDatabaseDetail -Json $fixtureProbe.Json -Url $fixtureProbe.Url
    Write-Host "OK 500 /api/fixture?torneo_id=1 (database unavailable, error sanitized)"
} else {
    throw "Expected fixture 200$(if (-not $RequireDatabase) { ' or sanitized 500' }) for $($fixtureProbe.Url), got $($fixtureProbe.StatusCode): $($fixtureProbe.Content)"
}

if ($CheckDatabase -or $RequireDatabase) {
    $dbProbe = Invoke-JsonProbe -Path "/api/health/db"

    if ($dbProbe.StatusCode -eq 200) {
        if ($dbProbe.Json.success -ne $true -or $dbProbe.Json.data.status -ne "ok") {
            throw "Invalid database health success payload from $($dbProbe.Url)"
        }
        Write-Host "OK 200 /api/health/db"
    } elseif ($dbProbe.StatusCode -eq 500 -and -not $RequireDatabase) {
        if ($dbProbe.Json.success -ne $false -or [int]$dbProbe.Json.error.code -ne 500) {
            throw "Invalid database health failure payload from $($dbProbe.Url)"
        }
        Assert-NoSensitiveDatabaseDetail -Json $dbProbe.Json -Url $dbProbe.Url
        Write-Host "OK 500 /api/health/db (database unavailable, error sanitized)"
    } else {
        throw "Expected database health 200$(if (-not $RequireDatabase) { ' or sanitized 500' }) for $($dbProbe.Url), got $($dbProbe.StatusCode): $($dbProbe.Content)"
    }

    if ($RequireDatabase) {
        Invoke-ContentCheck -Path "/api/torneos?status=I" -ExpectedStatus 200 -Assert {
            param($content, $url)
            if ($content -notmatch '"success"\s*:\s*true' -or $content -notmatch '"data"\s*:\s*\[') {
                throw "Invalid torneos payload from $url"
            }
        }
    }
} else {
    Write-Host "Skipping /api/health/db (use -CheckDatabase to include it)."
}

Write-Host "Smoke checks passed for $BaseUrl"
