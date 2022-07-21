<h2>Scan a List of IP Addresses</h2>

<p>Here is a basic example to scan a list of IP addresses from ips-list.txt file.</p>

<p>Make sure the file ips-list.txt contains one IP address per line.</p>

<hr style="margin: 20px 0;" />

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

$file = realpath(dirname(__FILE__))."/ips-list.txt";

if(!file_exists($file))
{
	echo '<p><font color="red">Cannot find ips-list.txt file!</font></p>';
}
else
{
	$ips_count = 0;
	
	$time_start = microtime(true);
	
	$handle = fopen($file, "r");
	
	if(!$handle) die('<p><font color="red">Failed to open ips-list.txt file!</font></p>');
	
	while(!feof($handle))
	{
		$ip = trim(fgets($handle));
		
		if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) continue;
		
		echo 'Scanning <strong>'.htmlspecialchars($ip).'</strong><br />';
			
		$ips_count++;
			
		$ip_reputation = curl_get_json('https://endpoint.apivoid.com/iprep/v1/pay-as-you-go/?key='.$_apivoid['key'].'&ip='.$ip);
		
		if(is_array($ip_reputation))
		{
			if(!isset($ip_reputation['error']))
			{
				echo "IP Reputation: ".( $ip_reputation['data']['report']['blacklists']['detections'] ? '<font color="red">Blacklisted ('.intval($ip_reputation['data']['report']['blacklists']['detections']).'/'.intval($ip_reputation['data']['report']['blacklists']['engines_count']).')</font>' : '<font color="green">Potentially Safe ('.intval($ip_reputation['data']['report']['blacklists']['detections']).'/'.intval($ip_reputation['data']['report']['blacklists']['engines_count']).')</font>' )."<br />";

				echo "Proxy/Tor/VPN: ".( ($ip_reputation['data']['report']['anonymity']['is_proxy'] || $ip_reputation['data']['report']['anonymity']['is_tor'] || $ip_reputation['data']['report']['anonymity']['is_vpn'] || $ip_reputation['data']['report']['anonymity']['is_webproxy']) ? "True" : "False" )."<br />";

        		echo "IP Location (Country): ".htmlspecialchars($ip_reputation['data']['report']['information']['country_name'])."<br />";
			
        		echo "ISP: ".htmlspecialchars($ip_reputation['data']['report']['information']['isp'])."<br />";
			}
			else
			{
				echo "<font color='red'>".htmlspecialchars($ip_reputation['error'])."</font><br />";
			}
		}
		else
		{
			echo "<font color='red'>Failed to get API data</font><br />";
		}
		
		echo "<hr style='margin: 20px 0;' />";
	}
	
	fclose($handle);
	
	echo '<p>Scanned '.number_format(intval($ips_count)).' IP addresses in total</p>';
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
