Install-Module -Name OneDrive
Install-Module -Name Microsoftteams
Connect-MicrosoftTeams
groupid = 7270cb64-cef4-4da0-9306-780d5d5059ac
msteams_5f06d0



om "User1@contoso.com" -To "channelemailaddress" -Subject "Test123" -SmtpServer "smtp.office365.com" -Credential "User1@contoso.com" -UseSsl -Attachments "E:\scripts\output.csv"