Set objshell = wscript.createobject("wscript.shell")
' do
objshell.run("C:\xampp\htdocs\pr_despacho\commands\bat\BackupPeriodico.bat"),0,true
objshell.run("C:\xampp\htdocs\pr_despacho\commands\bat\BackupPeriudicoGualberto.bat"),0,true
objshell.run("C:\xampp\htdocs\pr_despacho\commands\bat\BackupAllFiles.bat"),0,true

' Wscript.sleep 86400000 
' loop