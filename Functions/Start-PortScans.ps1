$ip = "10.10.10.161"
$box = "forest"

function Scan-Ports {
    [CmdletBinding()]
    param (
        [Parameter(Mandatory)]
        [string]$ip,
        [Parameter(Mandatory)]
        [string]$box
    )
    nmap -sV -sC -oA $box-sVsC $ip
    nmap -sUV -T4 -F --version-intensity 0 -oA $box-UDP $ip
    nmap -sV -p- -sC -oA $box-svc-p- $ip
    nmap -sUV -p- -sC -T4 -F --version-intensity 1 -oA $box-UDP-p- $ip
}
Scan-Ports $ip $box