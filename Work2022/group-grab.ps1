
# Check if modules installed, install
if(!(get-module -ListAvailable -name ActiveDirectory)){ Install-Module ActiveDirectory }
if(!(get-module -ListAvailable -name msonline)){ Install-Module MSonline }

Import-Module MSOnline
Import-Module ActiveDirectory


# Test if connected to MSOnline, prompt for credentials if not
try {
    Get-MsolDomain -ErrorAction Stop > $null
}
catch {
    Write-Output "Connecting to Office 365..."
    Connect-MsolService
}

# Gather O365 group membership
$MSOLGPS = Get-MsolGroup -all

# Create empty array to store our custom object
$Table1 = @()

# Create array for the o365 data itself
$DataMSOL = @()

# Create the hashtable to store whatever properties we want for the PSobject
$Record = @{
  "MSOLGroup" = ""
  "Member" = ""
}

# Start looping through groups in O365, then loop through members in each group, store the group objects displayname and the users displayname in our custom object
# Store custom object in Table which we will use later to create the document and then do error checking against it's contents
Foreach ($MSOLGP in $MSOLGPS) {
  Foreach ($Member in (Get-MsolGroupMember -GroupObjectId $MSOLGP.ObjectId))
    {
      $Record."MSOLGroup" = $MSOLGP.DisplayName
      $Record."UserName" = $Member.DisplayName
      $objRecord = New-Object PSObject -property $Record
      $Table1 += $objrecord
    }
}

# Echo Table and convert to CSV, export to user's home folder
$Table1 | export-csv "~\O365Groups.csv" -NoTypeInformation

# Get AD membership, output in 3 column CSV Name, SAM and Group
$ADGroups = (Get-AdGroup -filter * | Where {$_.name -like "**"} | select name -ExpandProperty name)

# Build our custom objects for AD groups
$Table2 = @()
$Record = @{
  "Group Name" = ""
  "Name" = ""
}

# Get loopin
Foreach ($Group in $ADGroups)
{
  $Arrayofmembers = Get-ADGroupMember -identity $Group -recursive | select name

  foreach ($Member in $Arrayofmembers)
  {
    $Record."Group Name" = $Group
    $Record."Name" = $Member.name
    $objRecord = New-Object PSObject -property $Record
    $Table2 += $objrecord
  }
}

# Echo Table and convert to CSV, export to user's home folder
$Table2 | export-csv "~\ADGroups.csv" -NoTypeInformation

# Test if the file was created for O365 groups
if(!(test-Path ~\O365Groups.csv)){
  Write-Output "Your O365 Groups output file was not created."
}
else{
  Write-Output "O365 Groups and Users output to O365Groups.csv"
}

# Test if the file was created for AD groups
if(!(test-Path ~\ADGroups.csv)){
  Write-Output "Your AD Groups output file was not created."}
else{
  Write-Output "Active Directory Groups and Users output to ADGroups.csv"
}

# If files exist, check the contents of the file against the Table we took them from. If the data does not match, most likely the file did not get updated
$o365 = Get-Content "~\O365Groups.csv"
$ad = Get-Content "~\ADGroups.csv"
$goodad = if(!($ad -eq $Table1)) { Write-Output "Check the  ADGroups.csv, contents of file do not match stored variable" }
$good0365 = if(!($o365 -eq $Table2)) { Write-Output "Check the O365Groups.csv, contents of file do not match stored variable" }

Write-Output "Finished pulling groups"

# Run our file checks for AD
if(test-path ~\ADGroups.csv)
  {Write-Output "AD file exists"}
else
  {Write-Output "AD Groups was not successful"}

# Run our filechecks for O365
if(test-path ~\O365Groups.csv)
  { Write-Output "O365 file exists"}
else
  {Write-Output "O365 Groups was not successful"}

# Say Goodbye
Write-Output "CSVs output to your home directory, for FG's perusal."








