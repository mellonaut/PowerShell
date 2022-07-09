# Base Image
FROM microsoft/nanoserver

# Copy powershell init-script from the host machine (windows) to the docker container.
COPY init-setup.ps1 c:\\workspace\application\init-setup.ps1

# Run the Powershell script in the Docker Container
CMD ["powershell.exe", "c:\\workspace\application\init-setup.ps1"]