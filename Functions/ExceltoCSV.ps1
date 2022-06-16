$path = 'C:\BadIdeas\WorkSL\PowerShellGoofin\dhcp'
$file = 'ActiveIPs.xlsx'
function ExcelToCSV ($file) {
    $Excel = New-Object -ComObject Excel.Application
    $wb = $Excel.Workbooks.Open($file)

    $x = $File | select-object Directory, Basename
    $n = [System.IO.Path]::Combine($x.Directory, (($x.BaseName, 'csv') -join "."))
    
    foreach ($ws in $wb.Worksheets) {
        $ws.SaveAs($n, 6)
    }
    $Excel.Quit()
}

Get-ChildItem $path\*.xlsx | 
    ForEach-Object{
        ExcelToCSV -File $_
    }