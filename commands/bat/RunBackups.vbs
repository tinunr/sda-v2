Set objshell = wscript.createobject("wscript.shell")
' do
objshell.run("C:\xampp\htdocs\pr_despacho\commands\bat\BackupMySQLDatabase.bat"),0,true
' objshell.run("C:\xampp\htdocs\pr_despacho\commands\bat\BackupAllFiles.bat"),0,true
' Wscript.sleep 3600000 
' loop