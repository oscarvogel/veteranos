$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = Split-Path -Parent $ScriptDir
$PhpLocal = Join-Path $ScriptDir 'php-local.ps1'
$Yiic = Join-Path $ProjectRoot 'protected\yiic'

& $PhpLocal $Yiic @args
exit $LASTEXITCODE
