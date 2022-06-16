function Request-GraphToken  {
    [CmdletBinding()]
    param (
        [Parameter(Mandatory = $true, HelpMessage="Enter your Tenant name")]
        [string]$tenant,
        [Parameter(Mandatory = $true, HelpMessage="Enter your Client Id")]
        [string]$client_id,
        [Parameter(Mandatory = $true, HelpMessage="Enter your Client Secret")]
        [string]$client_secret
    )



    $openId = Invoke-RestMethod -uri "https://login.microsoftonline.com/$tenant/.well-known/openid-configuration"

    Write-Output "The token endpoint for your directory is:"
    $openId.token_endpoint

    $token = $openId.token_endpoint

    $Body = @{
        client_id  = $client_id
        client_secret = $client_secret
        redirect_uri = "https://localhost"
        grant_type = "client_credentials"
        resource = "https://graph.microsoft.com"
        tenant = $tenant
    }
    Write-Output "Requesting access token..."

    $request = Invoke-RestMethod -uri $token -Body $Body -Method Post
    $request.access_token
}