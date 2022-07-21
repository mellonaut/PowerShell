<h2>Convert an URL to PDF</h2>

<p>An example to convert an URL to PDF and save the PDF as file.</p>

<p>Just enter the URL below and press the button:</p>

<hr style="margin: 20px 0;" />

<form action="" method="post">
  URL:
  <br />
  <input type="text" name="url" placeholder="" required>
  <br />
  <br />
  <input type="submit" name="submit" value="Submit">
</form> 

<?php

if(isset($_POST['submit']))
{
	require_once dirname(__DIR__, 1).'/config.php';
    require_once dirname(__DIR__, 1).'/helper.php';
	
	if(!$_apivoid['key']) die('<p><font color="red">You need to add your API key on config.php file!</font></p>');
	
	$url = trim($_POST['url']);
	
	// Make sure URL is valid
    if(!filter_var($url, FILTER_VALIDATE_URL)) die('<p><font color="red">URL is not valid!</font></p>');
	
	// Take screenshot with APIVoid Screenshot API
	$json = curl_get_json('https://endpoint.apivoid.com/urltopdf/v1/pay-as-you-go/?key='.$_apivoid['key'].'&url='.urlencode($url));
	
	// Make sure the API output is valid
	if(is_array($json))
	{
		// Make sure the API didn't return an error
		if(!isset($json['error']))
		{
            $save_as = realpath(dirname(__FILE__))."/document.pdf";
	
            file_put_contents($save_as, base64_decode($json['data']['base64_file']));
	
            if(file_exists($save_as))
            {
                echo '<p>File document.pdf saved successfully!</p>';
			
			    echo '<p><a href="document.pdf" target="_blank">Open PDF document&raquo;</a></p>';
            }
            else
            {
                echo '<p>Failed to create document.pdf file!</p>';
            }
		}
		else
		{
			echo "<p>".htmlspecialchars($json['error'])."<p>";
		}
    }
    else
    {
		echo "<p>Failed to convert URL to PDF!<p>";
    }
}

?>
