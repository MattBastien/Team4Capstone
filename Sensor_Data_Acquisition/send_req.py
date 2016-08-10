#!/usr/bin/python
import argparse
import requests

parser = argparse.ArgumentParser()
parser.add_argument('-d', dest='devCode')
parser.add_argument('-u', dest='value')
args = parser.parse_args()

print args.devCode, args.value

requests.post("http://localhost:8080", data={'devCode': args.devCode, 'value': args.value})
