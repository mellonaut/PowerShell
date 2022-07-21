#!/usr/bin/python3

import requests
import base64
import json
import sys
import os

apivoid_key = "YOUR_APIVOID_KEY_HERE";

html = "<h1>Testing</h1><p>Example text...</p>";

html_base64 = base64.b64encode(html.encode("utf-8"));

post_data = {'html':str(html_base64, "utf-8")}

try:
   r = requests.post(url='https://endpoint.apivoid.com/screenshot/v1/pay-as-you-go/?key='+apivoid_key+'&full_page=true', data=post_data)
   output = json.loads(r.content.decode());
except:
   print("Failed to get API output");
   sys.exit(1)
   
if(output.get('error')):
   print("Error: "+output['error'])
else:
   filename = "screeenshot.png";
   
   base64_file = output['data']['base64_file'];
   
   base64_file_decoded = base64.b64decode(base64_file).strip();
   
   save_as = open(filename, 'wb')
   
   save_as.write(base64_file_decoded)
   
   if(os.path.exists(filename) and os.path.isfile(filename) and os.stat(filename).st_size > 0):
      print("Screenshot created successfully")
   else:
      print("Failed to create screenshot")
   
	  
	  