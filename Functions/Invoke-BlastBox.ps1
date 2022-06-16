function Invoke-BlastBox
{
 # Function to create a pop-ip Malware Analysis machine from my Image Gallery
  [CmdletBinding()]
  param
  (
    [Parameter(Mandatory=$false, Position=0)]
    [System.String]
    $AdminUsername = "mellonaut",
    
    [Parameter(Mandatory=$false, Position=1)]
    [System.String]
    $AdminPassword = "Mellonaut3389!",
    
    [Parameter(Mandatory=$false, Position=2)]
    [System.String]
    $location = "East US",
    
    [Parameter(Mandatory=$false, Position=3)]
    [Object]
    $subscription = (Get-AzSubscription -TenantId $tenant | where-object -Property "State" -eq "Enabled"| select-object -Property Id),
    
    [Parameter(Mandatory=$false, Position=4)]
    [System.String]
    $VMName = 'blastbox',
    
    [Parameter(Mandatory=$false, Position=5)]
    [System.String]
    $resourceGroupName = 'MalwareLab'
  )
  
  $rg = az group create --name $resourceGroupName --location $location
  
  $vnet = az network vnet create --name 'BlastBox-VNET' --resource-group $resourceGroupName --subnet-name 'OpenAss-Subnet'
  
  $pubip = az network public-ip create --resource-group $resourceGroupName --name 'OpenAss-IP'
  
  $nsg = az network nsg create --name 'OpenAss-NSG' --resource-group $resourceGroupName
  
  $vnic = az network nic create --resource-group $resourceGroupName --name 'OpenAss-NIC' --vnet-name $vnet --subnet 'OpenAss-Subnet' --network-security-group $nsg --public-ip-address $pubip
  
  $vm = az vm create --resource-group $resourceGroupName --name $VMName --admin-username $AdminUsername --admin-password $AdminPassword --image "/subscriptions/3a96cba1-84f6-4a67-9bd7-268271a1d542/resourceGroups/mellonaut/providers/Microsoft.Compute/galleries/malwaremachines/images/MalwareDevelopment" --specialize m.Web
}

