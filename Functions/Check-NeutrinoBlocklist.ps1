function Check-NeutrinoBlocklist {
    [CmdletBinding()]
    param (
        [Parameter(Mandatory)]
        [string]$ip,
        [Parameter(Mandatory)]
        [string]$userId,
        [Parameter(Mandatory)]
        [string]$apiKey
    )
    $IPObject = Invoke-RestMethod -Method GET -Uri "https://neutrinoapi.net/ip-blocklist?user-id=$userId&api-key=$apiKey&ip=$ip"

    [PSCustomObject]@{

        Ip                  =  $IPObject.ip
        CIDR                =  $IPObject.cidr
        IsListed            =  $IPObject.is-listed
        IsHijacked          =  $IPObject.is-hijacked
        IsSpider            =  $IPObject.is-spider
        IsTor               =  $IPObject.is-tor
        IsProxy             =  $IPObject.is-proxy
        IsMalware           =  $IPObject.is-malware
        IsVpn               =  $IPObject.is-vpn
        IsBot               =  $IPObject.is-bot
        IsSpamBot           =  $IPObject.is-spam-bot
        IsExploitBot        =  $IPObject.is-exploit-bot
        ListCount           =  $IPObject.list-count
        Blocklists          =  $IPObject.blocklists
        LastSeen            =  $IPObject.last-seen
        Sensors             =  $IPObject.sensors
    }
}
Check-NeutrinoBlocklist $ip $userId $apiKey