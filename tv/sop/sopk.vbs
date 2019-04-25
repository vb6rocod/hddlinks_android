' ProcessKillLocal.vbs
' Sample VBScript to kill a program
' Author Guy Thomas http://computerperformance.co.uk/
' Version 2.7 - December 2010
' ------------------------ -------------------------------' 
Option Explicit
Dim objWMIService, objProcess, colProcess
Dim strComputer, strProcessKill 
strComputer = "."
strProcessKill = "'sop.exe'" 

Set objWMIService = GetObject("winmgmts:" _
& "{impersonationLevel=impersonate}!\\" _ 
& strComputer & "\root\cimv2") 

Set colProcess = objWMIService.ExecQuery _
("Select * from Win32_Process Where Name = " & strProcessKill )
For Each objProcess in colProcess
objProcess.Terminate()
Next 
WScript.Quit 
' End of WMI Example of a Kill Process