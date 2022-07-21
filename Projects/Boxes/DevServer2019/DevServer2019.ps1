# Disable ieEsc
function Disable-ieESC
{
$AdminKey = “HKLM:\SOFTWARE\Microsoft\Active Setup\Installed Components{A509B1A7-37EF-4b3f-8CFC-4F3A74704073}”
$UserKey = “HKLM:\SOFTWARE\Microsoft\Active Setup\Installed Components{A509B1A8-37EF-4b3f-8CFC-4F3A74704073}”
Set-ItemProperty -Path $AdminKey -Name “IsInstalled” -Value 0
Set-ItemProperty -Path $UserKey -Name “IsInstalled” -Value 0
Stop-Process -Name Explorer
Write-Host “IE Enhanced Security Configuration (ESC) has been disabled.” -ForegroundColor Green
}
Disable-ieESC



function Install-Boxstarter {
. { iwr -useb https://boxstarter.org/bootstrapper.ps1 } | iex; get-boxstarter –Force
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
Set-TimeZone -Name "Eastern Standard Time" -Verbose
}
Install-Boxstarter

function Clean-House {
Disable-BingSearch
Set-TaskbarOptions -Dock 'Bottom'
Set-ExplorerOptions -showHiddenFilesFoldersDrives -showFileExtensions
Update-Help
Enable-WindowsUpdate
Install-WindowsUpdate
}
Clean-House


function Install-Software {
    choco install tabby -y
    choco install googlechrome -y
    choco install firefox -y
    choco install brave -y
    choco install git -y
    choco install poshgit -y
    choco install wsl2 -y
    choco install vscode -y
    choco install vscode-powershell -y
    choco install openvpn-connect -y
    choco install wireguard -y
    choco install powertoys -y
    choco install sysinternals -y-y
}
Install-Software

function Enable-remoting {
Enable-PSRemoting -Force
Enable-RemoteDesktop
}
Enable-remoting