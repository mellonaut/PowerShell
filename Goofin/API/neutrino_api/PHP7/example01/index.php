<h2>Basic APIVoid Usage</h2>

<p>Here I simply test all available APIs from APIVoid.</p>

<hr style="margin: 20px 0;" />

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';

if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

// Get IP Reputation

echo "<h4>IP Reputation API</h4>";

$ip = "139.162.99.243";

$ip_reputation = curl_get_json('https://endpoint.apivoid.com/iprep/v1/pay-as-you-go/?key='.$_apivoid['key'].'&ip='.$ip);

echo "IP Address: ".$ip."<br />";

if(is_array($ip_reputation))
{
	if(!isset($ip_reputation['error']))
	{
        echo "Risk Score: ".intval($domain_reputation['data']['report']['risk_score']['result'])." / 100<br />";
        echo "Blacklist Status: ".intval($ip_reputation['data']['report']['blacklists']['detections'])." / ".intval($ip_reputation['data']['report']['blacklists']['engines_count'])."<br />";
        echo "Detection Rate: ".htmlspecialchars($ip_reputation['data']['report']['blacklists']['detection_rate'])."<br />";
        echo "Detected By: ".( intval($ip_reputation['data']['report']['blacklists']['detections']) > 0 ? get_engines_list_detected_by($ip_reputation['data']['report']['blacklists']['engines']) : "-" )."<br />";
        echo "Proxy/Tor/VPN: ".( ($ip_reputation['data']['report']['anonymity']['is_proxy'] || $ip_reputation['data']['report']['anonymity']['is_tor'] || $ip_reputation['data']['report']['anonymity']['is_vpn'] || $ip_reputation['data']['report']['anonymity']['is_webproxy']) ? "True" : "False" )."<br />";
        echo "IP Location: ".htmlspecialchars($ip_reputation['data']['report']['information']['country_name'])."<br />";
        echo "ISP: ".htmlspecialchars($ip_reputation['data']['report']['information']['isp'])."<br />";
	}
	else
    {
		echo "Error: ".htmlspecialchars($ip_reputation['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Get Domain Reputation

echo "<h4>Domain Reputation API</h4>";

$domain = "gumblar.cn";

$domain_reputation = curl_get_json('https://endpoint.apivoid.com/domainbl/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);

echo "Domain: ".ucfirst($domain)."<br />";

if(is_array($domain_reputation))
{
	if(!isset($domain_reputation['error']))
	{
        echo "Risk Score: ".intval($domain_reputation['data']['report']['risk_score']['result'])." / 100<br />";
        echo "Blacklist Status: ".intval($domain_reputation['data']['report']['blacklists']['detections'])." / ".intval($domain_reputation['data']['report']['blacklists']['engines_count'])."<br />";
        echo "Detection Rate: ".htmlspecialchars($domain_reputation['data']['report']['blacklists']['detection_rate'])."<br />";
        echo "Detected By: ".( intval($domain_reputation['data']['report']['blacklists']['detections']) > 0 ? get_engines_list_detected_by($domain_reputation['data']['report']['blacklists']['engines'], ", ", true) : "-" )."<br />";
        echo "Most Abused TLD: ".ucfirst(var_export($domain_reputation['data']['report']['security_checks']['is_most_abused_tld'], true))."<br />";
        echo "Website Popularity: ".ucfirst(htmlspecialchars($domain_reputation['data']['report']['security_checks']['website_popularity']))."<br />";
        echo "Server IP Address: ".htmlspecialchars($domain_reputation['data']['report']['server']['ip'])."<br />";
        echo "Server Location: ".htmlspecialchars($domain_reputation['data']['report']['server']['country_name'])."<br />";
        echo "Server ISP: ".htmlspecialchars($domain_reputation['data']['report']['server']['isp'])."<br />";
	}
	else
	{
		echo "Error: ".htmlspecialchars($domain_reputation['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Get Domain Age

echo "<h4>Domain Age API</h4>";

$domain = "facebook.com";

$domain_age = curl_get_json('https://endpoint.apivoid.com/domainage/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);

echo "Domain: ".ucfirst($domain)."<br />";

if(is_array($domain_age))
{
	if(!isset($domain_age['error']))
	{
        echo "Domain Age Found: ".ucfirst(var_export($domain_age['data']['domain_age_found'], true))."<br />";
        echo "Creation Date: ".htmlspecialchars($domain_age['data']['domain_creation_date'])."<br />";
        echo "Age in Days: ".number_format($domain_age['data']['domain_age_in_days'])."<br />";
        echo "Age in Months: ".number_format($domain_age['data']['domain_age_in_months'])."<br />";
        echo "Age in Years: ".number_format($domain_age['data']['domain_age_in_years'])."<br />";
	}
	else
	{
		echo "Error: ".htmlspecialchars($domain_age['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Get SSL Details

echo "<h4>SSL Info API</h4>";

$domain = "twitter.com";

$ssl_information = curl_get_json('https://endpoint.apivoid.com/sslinfo/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);

echo "Domain: ".ucfirst($domain)."<br />";

if(is_array($ssl_information))
{
	if(!isset($ssl_information['error']))
	{
        echo "SSL Found: ".ucfirst(var_export($ssl_information['data']['certificate']['found'], true))."<br />";
        echo "Certificate Valid Peer: ".ucfirst(var_export($ssl_information['data']['certificate']['valid_peer'], true))."<br />";
        echo "Certificate Blacklisted: ".ucfirst(var_export($ssl_information['data']['certificate']['blacklisted'], true))."<br />";
        echo "Certificate Issuer: ".htmlspecialchars($ssl_information['data']['certificate']['details']['issuer']['organization'])."<br />";
        echo "Certificate Deprecated Issuer: ".ucfirst(var_export($ssl_information['data']['certificate']['deprecated_issuer'], true))."<br />";
        echo "Certificate Expired: ".ucfirst(var_export($ssl_information['data']['certificate']['expired'], true))."<br />";
        echo "Certificate Valid: ".ucfirst(var_export($ssl_information['data']['certificate']['valid'], true))."<br />";
        echo "Expiration Date: ".htmlspecialchars($ssl_information['data']['certificate']['details']['validity']['valid_to'])."<br />";
        echo "Expiration Days Left: ".number_format($ssl_information['data']['certificate']['details']['validity']['days_left'])."<br />";
        echo "Organization: ".htmlspecialchars($ssl_information['data']['certificate']['details']['subject']['organization'])."<br />";
        echo "Country: ".htmlspecialchars($ssl_information['data']['certificate']['details']['subject']['country'])."<br />";
	}
	else
	{
		echo "Error: ".htmlspecialchars($ssl_information['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Check Email Verify API

echo "<h4>Email Verify API</h4>";

$email = "abcde@yopmail.com";

$email_verify = curl_get_json('https://endpoint.apivoid.com/emailverify/v1/pay-as-you-go/?key='.$_apivoid['key'].'&email='.$email);

echo "Email: ".ucfirst($email)."<br />";

if(is_array($email_verify))
{
	if(!isset($email_verify['error']))
	{
        echo "Valid Format: ".ucfirst(var_export($email_verify['data']['valid_format'], true))."<br />";
        echo "Disposable: ".ucfirst(var_export($email_verify['data']['disposable'], true))."<br />";
        echo "Role: ".ucfirst(var_export($email_verify['data']['role_address'], true))."<br />";
        echo "Valid TLD: ".ucfirst(var_export($email_verify['data']['valid_tld'], true))."<br />";
        echo "Has MX Records: ".ucfirst(var_export($email_verify['data']['has_mx_records'], true))."<br />";
        echo "Suspicious Username: ".ucfirst(var_export($email_verify['data']['suspicious_username'], true))."<br />";
        echo "Suspicious Domain: ".ucfirst(var_export($email_verify['data']['suspicious_domain'], true))."<br />";
        echo "Dirty Words Username: ".ucfirst(var_export($email_verify['data']['dirty_words_username'], true))."<br />";
        echo "Dirty Words Domain: ".ucfirst(var_export($email_verify['data']['dirty_words_domain'], true))."<br />";
        echo "Domain Popular: ".ucfirst(var_export($email_verify['data']['domain_popular'], true))."<br />";
        echo "Risky TLD: ".ucfirst(var_export($email_verify['data']['risky_tld'], true))."<br />";
        echo "Should Block: ".ucfirst(var_export($email_verify['data']['should_block'], true))."<br />";
        echo "Score: ".intval($email_verify['data']['score'])."<br />";
	}
	else
	{
		echo "Error: ".htmlspecialchars($email_verify['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Check DNS Lookup API (A Record)

echo "<h4>DNS Lookup API (A Record)</h4>";

$domain = "google.com";

$dns_lookup = curl_get_json('https://endpoint.apivoid.com/dnslookup/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain.'&action=dns-a');

echo "Domain: ".ucfirst($domain)."<br />";

if(is_array($dns_lookup))
{
	if(!isset($dns_lookup['error']))
	{
        foreach($dns_lookup['data']['records']['items'] as $record)
	    {
            echo "DNS A Record: ".ucfirst($record['host'])." ".$record['class']." ".$record['ttl']." ".$record['type']." ".$record['ip']."<br />";		
        }
	}
	else
	{
		echo "Error: ".htmlspecialchars($dns_lookup['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Check Parked Domain API

echo "<h4>Parked Domain API</h4>";

$domain = "videocode.com";

$parked_domain = curl_get_json('https://endpoint.apivoid.com/parkeddomain/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);

echo "Domain: ".ucfirst($domain)."<br />";

if(is_array($parked_domain))
{
	if(!isset($parked_domain['error']))
	{
        echo "Parked Domain: ".ucfirst(var_export($parked_domain['data']['parked_domain'], true))."<br />";
        echo "A Records Found: ".ucfirst(var_export($parked_domain['data']['a_records_found'], true))."<br />";
	}
	else
	{
		echo "Error: ".htmlspecialchars($parked_domain['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

// Check ThreatLog API

echo "<h4>ThreatLog API</h4>";

$domain = "rbcaca.com";

$threatlog = curl_get_json('https://endpoint.apivoid.com/threatlog/v1/pay-as-you-go/?key='.$_apivoid['key'].'&host='.$domain);

echo "Domain: ".ucfirst($domain)."<br />";

if(is_array($threatlog))
{
	if(!isset($threatlog['error']))
	{
        echo "Detected: ".ucfirst(var_export($threatlog['data']['threatlog']['detected'], true))."<br />";
	}
	else
	{
		echo "Error: ".htmlspecialchars($threatlog['error'])."<br />";
	}
}
else
{
    echo "Error: Failed to get API data<br />";
}

echo "<hr style='margin: 20px 0;' />";

?>
