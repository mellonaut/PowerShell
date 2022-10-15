$addPath = "C:\Users\RleeA\OneDrive\vsWorkspace\Code\GitHubProjects\Tradecraft\dotnet"

$regexAddPath = [regex]::Escape($addPath)
$arrPath = $env:Path -split ';' | Where-Object {$_ -notMatch 
"^$regexAddPath\\?"}

$env:Path = ($arrPath + $addPath) -join ';'



# Full add/remove path function
# Set-PathVariable {
#     param (
#         [string]$AddPath,
#         [string]$RemovePath
#     )
#     $regexPaths = @()
#     if ($PSBoundParameters.Keys -contains 'AddPath'){
#         $regexPaths += [regex]::Escape($AddPath)
#     }

#     if ($PSBoundParameters.Keys -contains 'RemovePath'){
#         $regexPaths += [regex]::Escape($RemovePath)
#     }
    
#     $arrPath = $env:Path -split ';'
#     foreach ($path in $regexPaths) {
#         $arrPath = $arrPath | Where-Object {$_ -notMatch "^$path\\?"}
#     }
#     $env:Path = ($arrPath + $addPath) -join ';'
# }