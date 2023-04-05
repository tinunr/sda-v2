@echo off
:: Start Variables
set BackupCmd=xcopy /s /c /d /e /h /i /r /y
:: PASTA A SERES COPIADOS
set MySQLBackup=C:\MySQLBackup\
set PR_DESPACHO=C:\xampp\htdocs\pr_despacho\
:: PASTA A SERES COPIADOS
set MySQLBackup_GUALBERTO=\\gualberto\recuperacao\MCDESPACE\MySQLBackup\
set PR_DESPACHO_GUALBERTO=\\gualberto\recuperacao\MCDESPACE\pr_despacho\
:: End Variables
:: Actual Script Starts Here!
echo+
echo STARTING BACKUP
:: 2. Create new backup set
if not exist "%MySQLBackup_GUALBERTO%" mkdir "%MySQLBackup_GUALBERTO%"
if not exist "%PR_DESPACHO%" mkdir "%PR_DESPACHO_GUALBERTO%"
echo+
echo BACKING UP FILES...
%BackupCmd% "%MySQLBackup%*.*" "%MySQLBackup_GUALBERTO%"
%BackupCmd% "%PR_DESPACHO%*.*" "%PR_DESPACHO_GUALBERTO%"
echo+
echo BACKUP COMPLETED!