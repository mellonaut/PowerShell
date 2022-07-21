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

# Install boxstarter with chocolatey and basic configuration
. { iwr -useb https://boxstarter.org/bootstrapper.ps1 } | iex; get-boxstarter –Force
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
Set-TimeZone -Name "Eastern Standard Time" -Verbose

# House keeping and Updates
Disable-BingSearch
Set-TaskbarOptions -Dock Bottom
Set-ExplorerOptions -showHiddenFilesFoldersDrives -showFileExtensions
Enable-PSRemoting -Force
Update-Help
Enable-WindowsUpdate
Install-WindowsUpdate
Enable-RemoteDesktop

# Install Software
choco install tabby -y
choco install googlechrome -y
choco install firefox -y
choco install brave -y
choco install git -y
choco install posh-git -y
choco install wsl2 -y
choco install vscode -y
choco install vscode-powershell -y
choco install openvpn -y
choco install powertoys -y
choco install sysinternals -y

# Dev Tools and Testing

# choco install visualstudio2019community -y
# choco install visualstudio2017buildtools -y
# choco install mingw
choco install python3 -y
choco install pip -y
choco install golang -y
choco install hugo -y
choco install nodejs -y 
choco install x64dbg.portable -y
choco install wireshark -y
choco install burp-suite-free-edition --pre 

# Cloud Tools

choco install awscli -y
choco install azure-cli -y
choco install doctl -y
choco install terraform
choco install cyberduck -y
choco install mremoteng -y
choco install vagrant -y
choco install packer -y
choco install vault -y
choco install kubernetes-cli -y