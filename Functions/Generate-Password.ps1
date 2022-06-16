$length = "32"
$int = $length
$Password = ([char[]]([char]33..[char]95) + ([char[]]([char]97..[char]126)) + 0..$int | sort {Get-Random})[0..$int] -join ''