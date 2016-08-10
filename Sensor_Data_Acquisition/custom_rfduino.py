from rfduino import RFduino
from MySQLBase import toggle
from pubsub import pub
import logging
import binascii

class DoorSensor(RFduino):
    def __init__(self, id, macAddr, name='Door Sensor'):
        RFduino.__init__(self, id, macAddr, name)
        pub.subscribe(self._listener, 'dev1')
    
    def _listener(self, arg1):
        self.setState(int(arg1))
    
    def onDataReceive(self, handle, data):
        RFduino.onDataReceive(self, handle, data)
        toggle('doorOpen', data[1])
        toggle('dev1', data[2])

class CurrentSensor(RFduino):
    def __init__(self, id, macAddr, name='Current Sensor'):
        RFduino.__init__(self, id, macAddr, name)
        pub.subscribe(self._listener, 'dev2')
    
    def _listener(self, arg1):
        self.setState(int(arg1))
    
    def onDataReceive(self, handle, data):
        RFduino.onDataReceive(self, handle, data)
        toggle('Current', data[1])
        toggle('dev2', data[2])
