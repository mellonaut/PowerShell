Install-MOdule -Name Microsoft.Graph
$Commands = Get-Command -Module Mcirosoft.Graph
$Commands.Count

Get-MgProfile
Select-MgProfile -Name "beta"
Select-MgProfile -Name "v1.0"

Connect-MgGraph -Scopes "User.Read.All","Directory.Read.All"

$users = Get-MgUser -All
$me = get-mguser

Connect-MgGraph -Scopes "User.ReadWrite.All","directory.ReadWrite.All"
