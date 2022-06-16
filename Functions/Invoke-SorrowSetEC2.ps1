$accesskey = ''
$secretkey = ''
$sorrowset = 'ami-07c5bfcfd08bee251'

Set-AwsCredential -AccessKey "$accesskey" -SecretKey "$secretkey" -StoreAs Default

# Set the region for our project
Initialize-AWSDefaultConfiguration -Region 'us-east-1'
# get-awscredentials -ListProfileDetail

# Set up the VPC, DNS, the Gateway and create a Route Table
$network = '10.0.0.0/16'
$vpc = New-EC2Vpc -CidrBlock $network
Edit-EC2VpcAttribute -VpcId $vpc.VpcId -EnableDnsSupport $true
Edit-EC2VpcAttribute -VpcId $vpc.VpcId -EnableDnsHostNames $true
$gw = New-EC2InternetGateway
$gw
Add-EC2InternetGateway -InternetGatewayId $gw.InternetGatewayId -VpcId $vpc.VpcId
$rt = New-Ec2RouteTable -VpcId $vpc.VPcId
$rt

# Add default route for the Gateway
New-Ec2Route -RouteTableId $rt.RouteTableId -GatewayId $gw.InternetGatewayId -DestinationCidrBlock '0.0.0.0/0'

#Create the subnet and attach it to the route table
$sn = New-Ec2Subnet -VpcId $vpc.VpcId -CidrBlock '10.0.1.0/24' -AvailabilityZone 'us-east-1a'
Register-EC2RouteTable -RouteTableId $rt.RouteTableId -SubnetId $sn.SubnetId


# $platform_values = New-Object 'collections.generic.list[string]'
# $platform_values.add("windows")
# $filter_platform = New-Object Amazon.EC2.Model.Filter -Property @{Name = "platform"; Values = $platform_values}
# Get-EC2Image -Owner amazon, self -Filter $filter_platform
$sorrowset = 'ami-07c5bfcfd08bee251'
$params = @{
    ImageId = "$sorrowset"
    AssociatePublicIp = $false
    InstanceType = 't2.micro'
    SubnetId = $sn.SubnetId
}
$ec2 = New-Ec2Instance @params
