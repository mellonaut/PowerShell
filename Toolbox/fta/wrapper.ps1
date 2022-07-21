# Place the PSFTA script and its wrapper in C:\scripts\fta
# Try scheduled "run whether logged in or not" "run as system" trigger "On workstation unlock" 
# Action "start a program NOQUOTES"= C:\scripts\fta\wrapper.ps1 Start-in (optional)= C:\scripts\fta conditions: Wake to run"
# Testing begun 12/15/21

# If we need to use PS transcription, uncomment and set the registry with "enabletranscript.ps1" then put "Stop"transcript" at end of script 
# $null = New-Item -Path C:\scripts\log -ItemType Directory -ErrorAction Ignore
# Start-Transcript -Append -Path 'C:\scripts\log\Get-Date-Transcript.txt'
# Stop-Transcript

#dot-source the PSFTA script 
. C:\scripts\fta\PSFTA.ps1

$ext = Get-FTA .pdf

If ($ext -eq "MSEdgePDF") {
    Set-FTA Acrobat.Document.DC .pdf 
    }

# Stop-Transcript
