###############################################################################
# Script for provisioning a Laptop for a new hire
# Installs features, hardens SMB, LLMNR and NBT-NS
# Installs basic software
###############################################################################

Write-Host "Setting execution policy..."
Set-ExecutionPolicy Bypass

# Get Desired HostName for the computer
$hostName = HostName
Write-Output "Current Hostname: $Name"
$compName = (Read-Host "Enter New Hostame")

# Desktop Path
$DesktopPath = [Environment]::GetFolderPath("Desktop")

# Set up Chocolatey
# Download the boxstarter bootstrap
. { iwr -useb https://boxstarter.org/bootstrapper.ps1 } | iex; Get-Boxstarter -Force

# No Boxstarter / In case you want to install chocolatey by itself
# [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# Create ITA user
Set-LocalUser -Name "ita" -PasswordNeverExpires 1

#Set admin password
do {
	Write-Host "`nEnter Admin password"
		$pwd1 = Read-Host "Password" -AsSecureString
		$pwd2 = Read-Host "Confirm Password" -AsSecureString
		$pwd1_text = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($pwd1))
		$pwd2_text = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($pwd2))
		
		if ($pwd1_text -ne $pwd2_text) {
		Write-Warning "`nPasswords do not match. Please try again."
		}
	}
	while ($pwd1_text -ne $pwd2_text)
	
	Write-Host "`n`nPasswords matched"
	$userPass = $pwd1
	$pwd1_text = 'a'
	$pwd2_text = 'a'
	
# set new PC name
Write-Host -ForegroundColor Green "`n`nSetting Computer name..."
if(!($hostName = $compName)){ Rename-Computer -NewName $compName }

if ((gwmi win32_computersystem).partofdomain -eq $true) {
    write-host -fore green "Host is already part of a domain"
} else {
    Add-Computer -DomainName anchorconst.local
}


# Enable .NET Framework
Write-Host -ForegroundColor Green "Enable .NET Framework"
Enable-WindowsOptionalFeature -Online -FeatureName NetFx3 -All

# Update security settings
#Disable LLMNR
Write-Host -ForegroundColor Green "Disabling LLMNR"
REG ADD  “HKLM\Software\policies\Microsoft\Windows NT\DNSClient”
REG ADD  “HKLM\Software\policies\Microsoft\Windows NT\DNSClient” /v ” EnableMulticast” /t REG_DWORD /d “0” /f

# Disable NBT-NS
Write-Host -ForegroundColor Green "Disabling NBT-NS"
$regkey = "HKLM:SYSTEM\CurrentControlSet\services\NetBT\Parameters\Interfaces"
Get-ChildItem $regkey |foreach { Set-ItemProperty -Path "$regkey\$($_.pschildname)" -Name NetbiosOptions -Value 2 -Verbose}

Write-Host -ForegroundColor Green "Enabling SMB signing as always"
# Enable SMB signing as 'always'
$Parameters = @{
    RequireSecuritySignature = $True
    EnableSecuritySignature = $True
    EncryptData = $True
    Confirm = $false
}
Set-SmbServerConfiguration @Parameters

# Disable Powershell 2.0 to prevent downgrade attacks
Disable-WindowsOptionalFeature -Online -FeatureName MicrosoftWindowsPowerShellV2Root


Write-Host "Initializing that chocolatey goodness"
choco feature enable -n allowGlobalConfirmation
choco feature enable -n allowEmptyChecksums

$Boxstarter.RebootOk=$true # Allow reboots?
$Boxstarter.NoPassword=$false # Is this a machine with no login password?
$Boxstarter.AutoLogin=$true # Save my password securely and auto-login after a reboot
if (Test-Path "C:\BGinfo\build.cfg" -PathType Leaf)
{
    REG ADD "HKLM\Software\Microsoft\Windows NT\CurrentVersion\Winlogon" /v AutoAdminLogon /t REG_SZ /d 1 /f
}
# Basic setup

Set-WindowsExplorerOptions -EnableShowFileExtensions
Disable-BingSearch
Disable-GameBarTips




# Set a nice wallpaper : 
write-host "Setting a nice wallpaper"
$web_dl = new-object System.Net.WebClient
$wallpaper_url = "https://tessier-ashpool.s3.us-east-1.amazonaws.com/corevalues.png"
$wallpaper_file = "C:\Users\Public\Pictures\desktop.jpg"
$web_dl.DownloadFile($wallpaper_url, $wallpaper_file)
reg add "HKEY_CURRENT_USER\Control Panel\Desktop" /v Wallpaper /t REG_SZ /d "C:\Users\Public\Pictures\desktop.jpg" /f
reg add "HKEY_CURRENT_USER\Control Panel\Desktop" /v WallpaperStyle /t REG_DWORD /d "0" /f 
reg add "HKEY_CURRENT_USER\Control Panel\Desktop" /v StretchWallpaper /t REG_DWORD /d "2" /f 
reg add "HKEY_CURRENT_USER\Control Panel\Colors" /v Background /t REG_SZ /d "0 0 0" /f

# Disable Invasive Privacy Settings
reg add "HKLM\SOFTWARE\Policies\Microsoft\Windows\OOBE" /v DisablePrivacyExperience /t REG_DWORD /d 1


###############################################################################
# Apps Features and Utilites
###############################################################################
choco install belarcadvisor
choco install adobereader 
choco install microsoft-teams
choco install office365business --params="/productid:O365ProPlusRetail" /exclude:
choco install microsoft-monitoring-agent


# Sysmon w/ custom configuration
mkdir 'C:\sysmon';
Invoke-WebRequest -Uri 'https://github.com/mellonaut/sysmon/raw/main/sysmon.zip' -OutFile 'C:\sysmon\sysmon.zip';
Expand-Archive 'c:\sysmon\sysmon.zip' -DestinationPath "C:\sysmon";
cd 'c:\sysmon';
c:\sysmon\sysmon.exe -acceptEula -i 'c:\sysmon\sysmonconfig.xml'

###############################################################################
# Enable and Run Updates
###############################################################################
Install-WindowsUpdate -MicrosoftUpdate -AcceptAll -AutoReboot

# Add domain users to admin group
Add-LocalGroupMember -Group 'Administrators' -Member "anchorconst\scarones"
Add-LocalGroupMember -Group 'Administrators' -Member "anchorconst\rmattingly"
Add-LocalGroupMember -Group 'Administrators' -Member "anchorconst\sysnology"

# Set Primary and Secondary DNS
Set-DnsClientServerAddress -interfacealias Ethernet* -serveraddresses (“10.215.0.4,192.168.2.6”)

# add scan folder
$share = "\\ais1\scan\general"
$lnk = "$DesktopPath\SCAN.lnk"
$wobj = New-Object -ComObject ("Wscript.Shell")
$shortcut = $wobj.CreateShortcut($lnk)
$shortcut.TargetPath = $share

###############################################################################
# Remove Bloatware
###############################################################################
Write-Host "Removing Roblox and Clippy..." -ForegroundColor "Yellow"

function removeApp {
	Param ([string]$appName)
	Write-Output "Trying to remove $appName"
	Get-AppxPackage $appName -AllUsers | Remove-AppxPackage
	Get-AppXProvisionedPackage -Online | Where DisplayName -like $appName | Remove-AppxProvisionedPackage -Online
}

$applicationList = @(
	"Microsoft.BingFinance"
	"Microsoft.3DBuilder"
	"Microsoft.BingFinance"
	"Microsoft.BingNews"
	"Microsoft.BingSports"
	"Microsoft.BingWeather"
	"Microsoft.CommsPhone"
	"Microsoft.Getstarted"
	"Microsoft.WindowsMaps"
	"*MarchofEmpires*"
	"Microsoft.GetHelp"
	"Microsoft.Messaging"
	"*Minecraft*"
	"Microsoft.MicrosoftOfficeHub"
	"Microsoft.OneConnect"
	"Microsoft.WindowsPhone"
	"*Solitaire*"
	"Microsoft.MicrosoftStickyNotes"
	"Microsoft.Office.Sway"
	"Microsoft.XboxApp"
	"Microsoft.XboxIdentityProvider"
	"Microsoft.ZuneMusic"
	"Microsoft.ZuneVideo"
	"Microsoft.NetworkSpeedTest"
	"Microsoft.FreshPaint"
	"Microsoft.Print3D"
	"*Autodesk*"
	"*BubbleWitch*"
    "king.com*"
    "G5*"
	"*Dell*"
	"*Facebook*"
	"*Keeper*"
	"*Netflix*"
	"*Twitter*"
	"*Plex*"
	"*.EclipseManager"
	"ActiproSoftwareLLC.562882FEEB491" # Code Writer
	"*.AdobePhotoshopExpress"
	"Microsoft.ZuneMusic"
	
);

foreach ($app in $applicationList) {
    removeApp $app
}

###############################################################################
# Remove Boxstarter
###############################################################################
# Remove the Chocolatey packages in a specific order!
# 'Boxstarter.Azure', 'Boxstarter.TestRunner', 'Boxstarter.WindowsUpdate', 'Boxstarter',
#    'Boxstarter.HyperV', 'Boxstarter.Chocolatey', 'Boxstarter.Bootstrapper', 'Boxstarter.WinConfig', 'BoxStarter.Common' |
#    ForEach-Object { choco uninstall $_ }

# # Remove the Boxstarter data folder
# Remove-Item -Path (Join-Path -Path $env:ProgramData -ChildPath 'Boxstarter') -Recurse -Force

# # Remove Boxstarter from the path in both the current session and the system
# $env:PATH = ($env:PATH -split ';' | Where-Object { $_ -notlike '*Boxstarter*' }) -join ';'
# [Environment]::SetEnvironmentVariable('PATH', $env:PATH, 'Machine')

# # Remove Boxstarter from the PSModulePath in both the current session and the system
# $env:PSModulePath = ($env:PSModulePath -split ';' | Where-Object { $_ -notlike '*Boxstarter*' }) -join ';'
# [Environment]::SetEnvironmentVariable('PSModulePath', $env:PSModulePath, 'Machine')
