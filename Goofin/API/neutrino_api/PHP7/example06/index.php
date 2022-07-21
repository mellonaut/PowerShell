<h2>Clean a List of Email Addresses</h2>

<p>Here is a basic example to clean a list of email addresses from emails-list.txt file.</p>

<p>This example will remove disposable emails, emails with risky TLD, emails with no MX records, etc.</p>

<p>Then it will also save the cleaned emails in a new file named emails-cleaned.txt</p>

<p>Make sure the file emails-list.txt contains one email address per line.</p>

<hr style="margin: 20px 0;" />

<?php

require_once dirname(__DIR__, 1).'/config.php';
require_once dirname(__DIR__, 1).'/helper.php';
	
if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');

$file = realpath(dirname(__FILE__))."/emails-list.txt";

if(!file_exists($file))
{
	echo '<p><font color="red">Cannot find emails-list.txt file!</font></p>';
}
else
{
	$emails_count = 0;
	
	$emails_cleaned = array();
	
	$time_start = microtime(true);
	
	$handle = fopen($file, "r");
	
	if(!$handle) die('<p><font color="red">Failed to open emails-list.txt file!</font></p>');
	
	while(!feof($handle))
	{
		$email = trim(fgets($handle));
		
		if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
			
		$emails_count++;
			
		$email_verify = curl_get_json('https://endpoint.apivoid.com/emailverify/v1/pay-as-you-go/?key='.$_apivoid['key'].'&email='.$email);
		
		if(is_array($email_verify) && !isset($email_verify['error']))
		{
		    if(!$email_verify['data']['valid_tld']) 
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: invalid TLD<br />';
				
				continue;
			}
		
		    if(!$email_verify['data']['valid_format']) 
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: invalid format<br />';
				
				continue;
			}
		
		    if(!$email_verify['data']['has_mx_records'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: no MX records<br />';
				
				continue;
			}
		
		    if($email_verify['data']['disposable'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: disposable/temporary email<br />';
				
				continue;
			}
		
		    if($email_verify['data']['risky_tld'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: risky TLD<br />';
				
				continue;
			}
		
		    if($email_verify['data']['police_domain'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: police-related domain<br />';
				
				continue;
			}
		
		    if($email_verify['data']['suspicious_username'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: suspicious username<br />';
				
				continue;
			}
		
		    if($email_verify['data']['dirty_words_username'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: dirty words on username<br />';
				
				continue;
			}
			
		    if($email_verify['data']['suspicious_domain'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: suspicious domain<br />';
				
				continue;
			}
		
		    if($email_verify['data']['dirty_words_domain'])
			{
				echo 'Email address <font color="red">'.htmlspecialchars($email).'</font> removed: dirty words on domain<br />';
				
				continue;
			}
		}
		
		echo 'Email address <font color="green">'.htmlspecialchars($email).'</font> kept!<br />';
		
		$emails_cleaned[] = $email;
	}
	
	fclose($handle);
	
    echo "<hr style='margin: 20px 0;' />";
	
	echo '<p>Scanned '.number_format(intval($emails_count)).' email addresses in total</p>';
	
	echo '<p>'.number_format(intval(count($emails_cleaned))).' email addresses have been classified as fine</p>';
	
	sort($emails_cleaned);
	
	file_put_contents(realpath(dirname(__FILE__))."/emails-cleaned.txt", implode("\n", $emails_cleaned));
	
	$time_end = microtime(true);

	$time_taken = number_format($time_end - $time_start, 2);
	
	echo "<p>Time taken ".htmlspecialchars($time_taken)." sec(s)</p>";
}

?>
