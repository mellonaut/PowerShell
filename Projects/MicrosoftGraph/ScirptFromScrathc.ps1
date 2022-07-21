$tenant = Read-Host ("Enter Your Tenant Name")
$openId = Invoke-RestMethod -uri "https://login.microsoftonline.com/$tenant/.well-known/openid-configuration"

Write-Output "The token endpoint for your directory is:"
$openId.token_endpoint

$token = $openId.token_endpoint

$Body = @{
    client_id  = "da6480f7-53ee-4175-a122-f6f981979606"
    client_secret = "i7g8Q~uTskWPnfvLPZhB448cF8a4rt84ee1SHb1K"
    redirect_uri = "https://localhost"
    grant_type = "client_credentials"
    resource = "https://graph.microsoft.com"
    tenant = $tenant
}
Write-Output "Requesting access token.."

$request = Invoke-RestMethod -uri $token -Body $Body -Method Post
$request.access_token
