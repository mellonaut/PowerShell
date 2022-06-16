$fullpath = 'C:\BadIdeas\WorkSL\PowerShellGoofin\dhcp\commas.csv'
$testpath = 'C:\BadIdeas\WorkSL\PowerShellGoofin\dhcp\commas2.csv'

(Get-Content -Path $fullpath -Raw) -replace '(?<!"),|,(?!")',' ' | out-file $fullpath
