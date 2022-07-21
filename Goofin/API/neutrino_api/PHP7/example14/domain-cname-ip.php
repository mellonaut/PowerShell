<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die("You need to add your API key on config.php file!\n");

$file = realpath(dirname(__FILE__))."/domains-list.txt";

if(!file_exists($file))
{
	echo "Cannot find domains-list.txt file!\n";
}
else
{
	$domains_count = 0;
	
	$time_start = microtime(true);
	
	$save_as = realpath(dirname(__FILE__))."/data.csv";
	
	$handle = fopen($file, "r");
	
	if(!$handle) die("Failed to open domains-list.txt file!\n");
	
	if(!file_exists($save_as)) file_put_contents($save_as, "domain,cname,cname_ip,date\n", FILE_APPEND | LOCK_EX);
	
	while(!feof($handle))
	{
		$domain = trim(fgets($handle));
		
		if(!$domain || stripos($domain, ".") === false) continue;
		
		if(preg_match('/^(http:|https:)/is', $domain)) $domain = parse_url($domain, PHP_URL_HOST);
		
		echo "Scanning ".htmlspecialchars($domain)."\n";
			
		$domains_count++;
			
		$domain_cname = curl_get_json('https://endpoint.apivoid.com/dnslookup/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain.'&action=dns-cname');

        if(isset($domain_cname['data']['records']['found']) && $domain_cname['data']['records']['found'])
		{
			$cname = $domain_cname['data']['records']['items'][0]['target'];
			
			echo "CNAME record found: ".htmlspecialchars($cname)."\n";
			
			$cname_dns_a = curl_get_json('https://endpoint.apivoid.com/dnslookup/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$cname.'&action=dns-a');
			
			if(isset($cname_dns_a['data']['records']['found']) && $cname_dns_a['data']['records']['found'])
			{
				$cname_ip = $cname_dns_a['data']['records']['items'][0]['ip'];
				
				echo "CNAME IP address found: ".htmlspecialchars($cname_ip)."\n";
			}
			else
			{
				$cname_ip = "";
				
				echo "CNAME IP address not found\n";
			}
		}
		else
		{
			$cname = "";
			
			$cname_ip = "";
			
			echo "CNAME record not found\n";
			
			echo "CNAME IP address not found\n";
		}
		
		$temp = "";
		
		$temp .= $domain.",";
		
		$temp .= $cname.",";
		
		$temp .= $cname_ip.",";
		
		$temp .= date("Y-m-d H:i:s");

		file_put_contents($save_as, $temp."\n", FILE_APPEND | LOCK_EX);
	}
	
	fclose($handle);

	echo "Scanned ".number_format(intval($domains_count))." domains in total\n";
	
	echo "Check the generated file ".htmlspecialchars(basename($save_as))."\n";
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "Time taken ".htmlspecialchars($time_taken)." sec(s)\n";
}

?>
