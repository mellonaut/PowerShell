$IPAddress = "192.168.0.10"
$IPByte = $IPAddress.Split(".")
$NewValue = ($IPByte[0]+"."+$IPByte[1]+"."+$IPByte[2]+".0/24")