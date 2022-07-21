$Path = "SOFTWARE\Microsoft\Windows\CurrentVersion\CapabilityAccessManager\ConsentStore\location"
# Enable location
New-ItemProperty -Path "HKLM:\$Path" -Name "Value" -Type String -Value "Allow" -Force
# Disable location
New-ItemProperty -Path "HKLM:\$Path" -Name "Value" -Type String -Value "Deny" -Force