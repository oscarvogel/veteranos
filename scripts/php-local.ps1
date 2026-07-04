$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = Split-Path -Parent $ScriptDir
$ProjectPhpExe = Join-Path $ProjectRoot 'tools\php-7.4.33\php.exe'
$PhpExe = if (Test-Path -LiteralPath $ProjectPhpExe) { $ProjectPhpExe } else { 'C:\xampp\php\php.exe' }

if (-not (Test-Path -LiteralPath $PhpExe)) {
    throw "No se encontro PHP local en $PhpExe"
}

if ($PhpExe -eq $ProjectPhpExe) {
    $ExtensionDir = Join-Path (Split-Path -Parent $ProjectPhpExe) 'ext'
    & $PhpExe -n `
        -d "extension_dir=$ExtensionDir" `
        -d extension=php_pdo_mysql.dll `
        -d extension=php_mysqli.dll `
        -d extension=php_mbstring.dll `
        -d extension=php_gd2.dll `
        -d extension=php_openssl.dll `
        -d date.timezone=America/Argentina/Buenos_Aires `
        @args
} else {
    & $PhpExe @args
}
exit $LASTEXITCODE
