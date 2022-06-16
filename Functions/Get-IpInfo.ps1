function Get-IPInfo {
    [CmdletBinding()]
    param (
        [Parameter(Mandatory)]
        [string]$ip
    )
    $IPObject = Invoke-RestMethod -Method GET -Uri "https://ipapi.co/$ip/json"

    [PSCustomObject]@{
        IP        =  $IPObject.IP
        City      =  $IPObject.City
        Country   =  $IPObject.Country_Name
        Region    =  $IPObject.Region
        Postal    =  $IPObject.Postal
        TimeZone  =  $IPObject.TimeZone
        ASN       =  $IPObject.asn
        Owner     =  $IPObject.org
    }
}
Get-IPInfo $ip