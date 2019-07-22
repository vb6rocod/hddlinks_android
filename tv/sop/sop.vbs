Set WshShell = WScript.CreateObject("WScript.Shell") 
Return = WshShell.Run("sop\sop.exe sop://broker.sopcast.com:3912/148084", 0)
Set ws=CreateObject("WScript.Shell")