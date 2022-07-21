#!/usr/bin/python3

# To run the script just use this:
# python3 /path/to/script.py "https://www.google.com/" "/tmp/document.pdf"

import requests
import urllib.parse
import base64
import json
import sys
import os

apivoid_key = "YOUR_APIVOID_KEY_HERE";

try:
   url = sys.argv[1];
   filename = sys.argv[2];
except:
   print("Usage: " + os.path.basename(__file__) + " <url> <path>")
   sys.exit(1)

def apivoid_urltopdf_url(key, url):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/urltopdf/v1/pay-as-you-go/?key='+key+'&url='+urllib.parse.quote(url))
      return json.loads(r.content.decode())
   except:
      return ""

data = apivoid_urltopdf_url(apivoid_key, url)
   
if(data.get('error')):
   print("Error: "+data['error'])
else:
   base64_file = data['data']['base64_file'];
   
   base64_file_decoded = base64.b64decode(base64_file).strip();
   
   save_as = open(filename, 'wb')
   
   save_as.write(base64_file_decoded)
   
   if(os.path.exists(filename) and os.path.isfile(filename) and os.stat(filename).st_size > 0):
      print("PDF document created successfully")
   else:
      print("Failed to create PDF document")
   
	  
	  