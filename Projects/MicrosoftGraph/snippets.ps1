Import-Module Microsoft.Graph.*
# find permissions you have
Find-MgGraphCommand -command Get-MgUser | Select -First 1 -ExpandProperty Permissions
# connect your session with those permissions (signs the JWT with these)

# To Trigger new session using the ConnectMgGraph script
.\ConnectMgGraph.ps1 -CreateSession

# try this
Connect–MgGraph –TenantId ffb763ee-1ffa-4bd2-bf1a-1ca02ba6d6a2
# normal rights
Connect-MgGraph -Scopes "User.ReadWrite.All","Group.ReadWrite.All"

# Teams create stuff
Connect-MgGraph -Scopes "Directory.ReadWrite.All"

# for anchor
Connect-MgGraph –Scopes "User.Read.All"
anchor a60f457b-77b2-4196-94bb-a2a7787299b0
dev account Mellonaut@7p66yn.onmicrosoft.com    ffb763ee-1ffa-4bd2-bf1a-1ca02ba6d6a2

# get yo self
Get-MgUser
$user = Get-MgUser -Filter "displayName eq 'mellonaut'"

Get-Command -Module Microsoft.Graph.Users 

# Get the Teams this use has joined
Get-MgUserJoinedTeam -UserId $user.Id


the MSFT way



# Load settings
$settings = Get-Content './settings.json' -ErrorAction Stop | Out-String | ConvertFrom-Json

$clientId = $settings.clientId
$authTenant = $settings.authTenant
$graphScopes = $settings.graphUserScopes

# <UserAuthSnippet>
# Authenticate the user
Connect-MgGraph -ClientId $clientId -TenantId $authTenant -Scopes $graphScopes -UseDeviceAuthentication
# </UserAuthSnippet>

# <GetContextSnippet>
# Get the Graph context
Get-MgContext
# </GetContextSnippet>

# <SaveContextSnippet>
$context = Get-MgContext
# </SaveContextSnippet>

# <GetUserSnippet>
# Get the authenticated user by UPN
$user = Get-MgUser -UserId $context.Account -Select 'displayName, id, mail, userPrincipalName'