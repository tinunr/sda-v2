@echo off
for /f "delims=" %%a in ('wmic OS Get localdatetime ^| find "."') do set DateTime=%%a

set Yr=%DateTime:~0,4%
set Mon=%DateTime:~4,2%
set Day=%DateTime:~6,2%
set Hr=%DateTime:~8,2%
set Min=%DateTime:~10,2%
set Sec=%DateTime:~12,2%

set BackupTarget=C:\MySQLBackup\

if not exist "%BackupTarget%%Yr%\" mkdir "%BackupTarget%%Yr%\"
if not exist "%BackupTarget%%Yr%\%Mon%\" mkdir "%BackupTarget%%Yr%\%Mon%\"
if not exist "%BackupTarget%%Yr%\%Mon%\%Day%" mkdir "%BackupTarget%%Yr%\%Mon%\%Day%\"

set BackupTargetDay="%BackupTarget%%Yr%\%Mon%\%Day%\"

set BackupName=pr_despachos_%Yr%-%Mon%-%Day%_(%Hr%-%Min%-%Sec%)

C:\xampp\mysql\bin\mysqldump -u root -p @Prd3bd@2020 -host 192.168.1.175 pr_despachos > "%BackupTargetDay%%BackupName%.sql"
