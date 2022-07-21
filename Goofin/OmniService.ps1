$credential = Get-Credential
Connect-AzureAD -Credential $credential
Connect-MsolService -Credential $credential
$orgName="sludge.one"
Import-Module Microsoft.Online.SharePoint.PowerShell -DisableNameChecking
Connect-SPOService -Url https://$orgName-admin.sharepoint.com -Credential $Credential
Import-Module ExchangeOnlineManagement
Connect-ExchangeOnline -ShowProgress $true
$acctName="<UPN of the account, such as belindan@litwareinc.onmicrosoft.com>"
Connect-IPPSSession -UserPrincipalName $acctName
Import-Module MicrosoftTeams
$credential = Get-Credential
Connect-MicrosoftTeams -Credential $credential