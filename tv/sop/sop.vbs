Set WshShell = WScript.CreateObject("WScript.Shell") 
Return = WshShell.Run("sop\sop.exe sop://185.224.90.82:3912/264939", 0)
Set ws=CreateObject("WScript.Shell")