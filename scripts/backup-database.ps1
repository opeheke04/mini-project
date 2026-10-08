param(
    [string] $DatabaseHost = "127.0.0.1",
    [int] $DatabasePort = 3306,
    [string] $DatabaseName,
    [string] $DatabaseUser = "root",
    [switch] $NoPassword,
    [string] $MysqldumpPath = "C:\xampp\mysql\bin\mysqldump.exe",
    [string] $BackupDirectory = "storage\app\private\database-backups"
)

$ErrorActionPreference = "Stop"

if ([string]::IsNullOrWhiteSpace($DatabaseName)) {
    throw "Supply the database name with -DatabaseName."
}

if (!(Test-Path -LiteralPath $MysqldumpPath -PathType Leaf)) {
    throw "mysqldump was not found at '$MysqldumpPath'. Supply the installed mysqldump.exe path."
}

$resolvedBackupDirectory = Join-Path (Get-Location) $BackupDirectory
New-Item -ItemType Directory -Path $resolvedBackupDirectory -Force | Out-Null

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$safeDatabaseName = $DatabaseName -replace "[^A-Za-z0-9_.-]", "_"
$backupPath = Join-Path $resolvedBackupDirectory "$safeDatabaseName-$timestamp.sql"
$arguments = @(
    "--host=$DatabaseHost"
    "--port=$DatabasePort"
    "--user=$DatabaseUser"
)

if (!$NoPassword) {
    $arguments += "--password"
}

$arguments += @(
    "--single-transaction"
    "--routines"
    "--triggers"
    "--result-file=$backupPath"
    "--databases"
    $DatabaseName
)

& $MysqldumpPath @arguments

if ($LASTEXITCODE -ne 0) {
    if (Test-Path -LiteralPath $backupPath) {
        Remove-Item -LiteralPath $backupPath -Force
    }

    throw "mysqldump failed with exit code $LASTEXITCODE; no backup was retained."
}

if (!(Test-Path -LiteralPath $backupPath -PathType Leaf) -or (Get-Item -LiteralPath $backupPath).Length -eq 0) {
    throw "mysqldump returned success but did not create a non-empty backup file."
}

Write-Output "Database backup created: $backupPath"
