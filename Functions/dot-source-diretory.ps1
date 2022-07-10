function Dot-Directory {
    param(
        [ValidateNotNullOrEmpty]
        [string]$Path
    )
Get-ChildItem -Path $Path -Filter *.ps1 |ForEach-Object {
    . $_.FullName
}
}
$Path = "."
Dot-Directory $Path