#!/usr/bin/python3

import requests

apivoid_key = "YOUR_APIVOID_KEY_HERE";

ip = "122.226.181.165";

r = requests.get(url='https://endpoint.apivoid.com/iprep/v1/pay-as-you-go/?key='+apivoid_key+'&ip='+ip)

print(r.json())
