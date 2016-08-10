from sensors import Sensor
import logging
import binascii

ID_SET_INTERVAL = 0x00
ID_SET_STATE    = 0x01

UUID_RFDUINO_DATA   = "c97433f1-be8f-4dc8-b6f0-5343e6100eb4"
UUID_RFDUINO_CONFIG = "c97433f2-be8f-4dc8-b6f0-5343e6100eb4"

class RFduino(Sensor):
    def __init__(self, id, macAddr, name='RFduino'):
        Sensor.__init__(self, id, macAddr, name)
        self.last_seq = 0
    
    def connect(self, adapter):
        logging.info('connecting to %s (%s)...' % (self.name, self.MAC))
        self.device = adapter.connect(self.MAC, 5, 1, 60, 76, 100, 0)
        self.setUpdateInterval(5000)
        self.device.subscribe(UUID_RFDUINO_DATA, self.onDataReceive)
    
    def configure(self, data):
        self.device.char_write_handle(self.device.get_handle(UUID_RFDUINO_CONFIG), data)
    
    def setUpdateInterval(self, interval):
        self.configure([ID_SET_INTERVAL, (interval >> 8) & 0xFF, interval & 0xFF])
    
    def setState(self, state):
        self.configure([ID_SET_STATE, state & 0xFF])
    
    def onDataReceive(self, handle, data):
        logging.info('from %s, received %s' % (self.name, binascii.hexlify(data)))
        return