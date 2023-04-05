@echo off
:: Start Variables
set BackupCmd=xcopy /s /c /d /e /h /i /r /y
set BackupSource=C:\xampp\htdocs\pr_despacho\
set BackupTarget=\\gualberto\recuperacao\MCDESPACE\_DATA\

for /f "delims=" %%a in ('wmic OS Get localdatetime ^| find "."') do set DateTime=%%a

set Yr=%DateTime:~0,4%
set Mon=%DateTime:~4,2%
set Day=%DateTime:~6,2%
set Hr=%DateTime:~8,2%
set Min=%DateTime:~10,2%
set Sec=%DateTime:~12,2%



if not exist "%BackupTarget%%Yr%\" mkdir "%BackupTarget%%Yr%\"
if not exist "%BackupTarget%%Yr%\%Mon%\" mkdir "%BackupTarget%%Yr%\%Mon%\"
if not exist "%BackupTarget%%Yr%\%Mon%\" mkdir "%BackupTarget%%Yr%\%Mon%\"
set BackupTargetDay="%BackupTarget%%Yr%\%Mon%\"

set BackupName=pr_despachos_%Yr%-%Mon%-%Day%_(%Hr%-%Min%-%Sec%)

:: End Variables
:: Actual Script Starts Here!
echo+
echo STARTING BACKUP
:: 1. Delete older backup set(s) beyond the NumberToKeep
::for /F "tokens=* skip=%NumberToKeep%" %%I In ('dir "%BackupTarget%" /AD /B /O-D /TW') do (
::	echo+
::	echo DELETING OLD BACKUP SET %BackupTarget%%%~I
::	rd /s /q "%BackupTarget%%%~I"
::)
:: 2. Create new backup set
echo+
echo BACKING UP FILES...
%BackupCmd% "%BackupSource%*.*" "%BackupTargetDay%\%BackupName%"
echo+
echo BACKUP COMPLETED!
echo %date% %time%