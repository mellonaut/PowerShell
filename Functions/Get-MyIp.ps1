function Get-MyIp {
    Invoke-RestMethod -Method GET -Uri "http://ifconfig.me/ip"
}
$ip = Get-MyIp