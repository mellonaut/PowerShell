function Remove-SpecialChars {
    param(
    [string]$string
    )
    
    $string = $string -replace '[^\p{L}\p{Nd}]', ''
    Write-Output $string
}
Remove-SpecialChars $string
