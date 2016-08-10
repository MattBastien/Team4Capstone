#!/usr/bin/env python
import time
import sensors
from pubsub import pub

# parse arguments
import argparse
parser = argparse.ArgumentParser()
parser.add_argument('-v', '--verbose', dest='verbose', action='store_true')
args = parser.parse_args()

# setup basic logging
import logging
if (args.verbose):
    logging.basicConfig(level=logging.INFO)
else:
    logging.basicConfig(level=logging.ERROR)

# Create the sensors and start data acquisition
from sensortag import SensorTag
from custom_rfduino import DoorSensor, CurrentSensor

sm = sensors.SensorManager()
sm.addSensor(SensorTag(0, 'C4:BE:84:70:75:87'))
sm.addSensor(CurrentSensor(1, 'EF:B9:74:5E:B5:19'))
sm.addSensor(DoorSensor(2, 'DC:C8:8E:E8:D4:02'))
sm.start()

try:
    # setup REST server
    import json
    from bottle import run, post, request, response, route

    @route('/', method='POST')
    def index():
        devCode = request.forms.get('devCode')
        value = request.forms.get('value')
        pub.sendMessage(devCode, arg1=value)
        return 'All done'
    run(host='localhost', port=8080, quiet=True)
finally:
    sm.stop()
