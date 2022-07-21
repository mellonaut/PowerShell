$APIKey = Get-Content '.\bing.api'
$url = "https://api.bing.microsoft.com/v7.0/images/search?q=bigbutt"
$body = @{
    search = "search index=_internal | reverse | table index,host,source,sourcetype,_raw"
    output_mode = "csv"
    earliest_time = "-2d@d"
    latest_time = "-1d@d"
}
$headers = @{
    'Ocp-Apim-Subscription-Key' = "00379c6e1d4a46edbca833a17a6fd542"
}

Invoke-RestMethod -method 'Get' -uri $url -Body $body -Headers $headers -Outfile output.csv
