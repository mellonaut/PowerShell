# $specificIpParams = @{
#     ResourceGroupName = 'myresourcegroup'
#     Name = 'myacr'
#     ResourceProviderName = 'Microsoft.ContainerRegistry'
#     ResourceType = 'registries'
#     ApiVersion = '2019-12-01-preview'
#     Payload = '{ "properties": {
#         "networkRuleSet": {
#         "defaultAction": "Deny",
#         "ipRules": [ {
#            "action": "Allow",
#            "value": "24.22.123.123"
#            } ]
#         }
#     } }'
#     -Method = 'PATCH'
#   }
#   Invoke-AzRestMethod @specificIpParams