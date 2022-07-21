<?php

<h2>Scan a List of Websites</h2>

<p>Here is a basic example to scan a list of websites from websites-list.txt file via PHP7-CLI.</p>

<p>Make sure the file websites-list.txt contains one website per line.</p>

<p>It will be created a CSV file data.csv with following details:</p>

<p>domain,risk_score,detections,detected_by,ip,hostname,country_code,isp,scan_date</p>

<p>You can run the script like this:</p>

<p>php -f domain-reputation.php</p>

?>