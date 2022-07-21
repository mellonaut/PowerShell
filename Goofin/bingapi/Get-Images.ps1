Install-PackageProvider -Name NuGet -Scope CurrentUser -Force
Install-Module BingCmdlets -Scope CurrentUser -Force
Import-Module BingCmdlets


$APIKey = Get-Content '.\bing.api'
$bing =  Connect-Bing -APIKey "$APIKey"

$searchTerms = '"Big Butt" + "Yoga Pants"'
$imageSearch = Select-Bing -Connection $bing -Table "ImageSearch" -Where "SearchTerms = `'$searchTerms`'"
$imageSearch



