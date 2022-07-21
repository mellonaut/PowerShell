#!/usr/bin/python3

# To run the script just use this:
# python3 /path/to/script.py /path/to/file/with/urls.txt /path/to/output/file.csv

import requests
import urllib.parse
import json
import sys
import os

apivoid_key = "YOUR_APIVOID_KEY_HERE";

try:
   my_file = sys.argv[1];
   csv_file = sys.argv[2];
except:
   print("Usage: " + os.path.basename(__file__) + " </path/to/file/with/urls.txt> </path/to/output/file.csv>")
   sys.exit(1)

def apivoid_urlstatus(key, url):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/urlstatus/v1/pay-as-you-go/?key='+key+'&url='+urllib.parse.quote(url))
      return json.loads(r.content.decode())
   except:
      return ""

def submit_url(url):
    data = apivoid_urlstatus(apivoid_key, url)
    if(data):
        if(data.get('error')):
            print("Error: "+data['error'])
        else:
            with open(csv_file, 'a') as f:
                #host,url_encoded,server_ip,http_status_code,suspended_page,sinkholed_domain,url_taken_down,url_status
                f.write(str(data['data']['report']['url_parts']['host'])+","+str(url)+","+str(data['data']['report']['server_details']['ip'])+","+str(data['data']['report']['analysis']['http_status_code'])+","+str(data['data']['report']['analysis']['suspended_page'])+","+str(data['data']['report']['analysis']['sinkholed_domain'])+","+str(data['data']['report']['analysis']['url_taken_down'])+","+str(data['data']['report']['analysis']['url_status'])+"\n")
    else:
        print("Error: Request failed")
   
def scan_file(f):
    if( not os.path.isfile(f)):
        print("File not found")
        sys.exit(1)
        
    try:
        with open(f) as fh:
            for line in fh:
                line = line.strip()
                if line:
                    submit_url(line)
                
    except IOError:
        print("File not accessible")
    
scan_file(my_file)