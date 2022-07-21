#!/usr/bin/python3

# To run the script just use this:
# python3 /path/to/script.py /path/to/file/with/domains.txt /path/to/output/file.csv

import requests
import json
import sys
import os

apivoid_key = "YOUR_APIVOID_KEY_HERE";

try:
   my_file = sys.argv[1];
   csv_file = sys.argv[2];
except:
   print("Usage: " + os.path.basename(__file__) + " </path/to/file/with/domains.txt> </path/to/output/file.csv>")
   sys.exit(1)

def apivoid_domainage(key, host):
   try:
      r = requests.get(url='https://endpoint.apivoid.com/domainage/v1/pay-as-you-go/?key='+key+'&host='+host)
      return json.loads(r.content.decode())
   except:
      return ""

def submit_domain(host):
    data = apivoid_domainage(apivoid_key, host)
    if(data):
        if(data.get('error')):
            print("Error: "+data['error'])
        else:
            with open(csv_file, 'a') as f:
                #domain,domain_registered,domain_creation_date,domain_age_in_days,domain_age_in_months,domain_age_in_years
                f.write(str(data['data']['host'])+","+str(data['data']['domain_registered'])+","+str(data['data']['domain_creation_date'])+","+str(data['data']['domain_age_in_days'])+","+str(data['data']['domain_age_in_months'])+","+str(data['data']['domain_age_in_years'])+"\n")
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
                    submit_domain(line)
                
    except IOError:
        print("File not accessible")
    
scan_file(my_file)