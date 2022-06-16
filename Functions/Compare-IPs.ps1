$scope = Get-Content .\'scope.csv'
$report = Get-Content .\'ActiveIPs.csv'

$diff = ($report | ?{$scope -notcontains $_})