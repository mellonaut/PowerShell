<h2>Scan a List of Websites</h2>

<p>Here is a basic example to scan a list of websites from websites-list.txt file.</p>

<p>Make sure the file websites-list.txt contains one website per line.</p>

<hr style="margin: 20px 0;" />

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

$file = realpath(dirname(__FILE__))."/websites-list.txt";

if(!file_exists($file))
{
	echo '<p><font color="red">Cannot find websites-list.txt file!</font></p>';
}
else
{
	$domains_count = 0;
	
	$time_start = microtime(true);
	
	$handle = fopen($file, "r");
	
	if(!$handle) die('<p><font color="red">Failed to open websites-list.txt file!</font></p>');
	
	while(!feof($handle))
	{
		$domain = trim(fgets($handle));
		
		if(!$domain || stripos($domain, ".") === false) continue;
		
		if(preg_match('/^(http:|https:)/is', $domain)) $domain = parse_url($domain, PHP_URL_HOST);
		
		$domain = preg_replace('/^www\./', '', $domain);
		
		echo 'Scanning <strong>'.htmlspecialchars($domain).'</strong><br />';
			
		$domains_count++;
			
		$domain_reputation = curl_get_json('https://endpoint.apivoid.com/domainbl/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);
			
		if(is_array($domain_reputation))
		{
			if(!isset($domain_reputation['error']))
			{
				echo "Website Reputation: ".( $domain_reputation['data']['report']['blacklists']['detections'] ? '<font color="red">Blacklisted ('.intval($domain_reputation['data']['report']['blacklists']['detections']).'/'.intval($domain_reputation['data']['report']['blacklists']['engines_count']).')</font>' : '<font color="green">Potentially Safe ('.intval($domain_reputation['data']['report']['blacklists']['detections']).'/'.intval($domain_reputation['data']['report']['blacklists']['engines_count']).')</font>' )."<br />";

				echo "Server IP Address: ".htmlspecialchars($domain_reputation['data']['report']['server']['ip'])."<br />";

        		echo "IP Location (Country): ".htmlspecialchars($domain_reputation['data']['report']['server']['country_name'])."<br />";
			
        		echo "ISP: ".htmlspecialchars($domain_reputation['data']['report']['server']['isp'])."<br />";
			}
			else
			{
				echo "<font color='red'>".htmlspecialchars($domain_reputation['error'])."</font><br />";
			}
		}
		else
		{
			echo "<font color='red'>Failed to get API data</font><br />";
		}
		
		echo "<hr style='margin: 20px 0;' />";
	}
	
	fclose($handle);
	
	echo '<p>Scanned '.number_format(intval($domains_count)).' websites in total</p>';
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
