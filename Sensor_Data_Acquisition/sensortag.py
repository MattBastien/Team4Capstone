from sensors import Sensor
from MySQLBase import sensorDump
import logging

UUID_IO_DATA      = 'F000AA65-0451-4000-B000-000000000000'
UUID_IO_CONFIG    = 'F000AA66-0451-4000-B000-000000000000' # 0 for local mode, 1 for remote mode, 2 for test mode

UUID_IRT_DATA     = 'F000AA01-0451-4000-B000-000000000000'
UUID_IRT_ENABLE   = 'F000AA02-0451-4000-B000-000000000000' # 0 to disable, 1 to enable
UUID_IRT_INTERVAL = 'F000AA03-0451-4000-B000-000000000000' # interval in tens of milliseconds

UUID_HUM_DATA     = 'F000AA31-0451-4000-B000-000000000000'
UUID_HUM_ENABLE   = 'F000AA32-0451-4000-B000-000000000000' # 0 to disable 1 to enable
UUID_HUM_INTERVAL = 'F000AA33-0451-4000-B000-000000000000' # interval in tens of milliseconds

UUID_BAR_DATA     = 'F000AA41-0451-4000-B000-000000000000'
UUID_BAR_ENABLE   = 'F000AA42-0451-4000-B000-000000000000' # 0 to disable 1 to enable
UUID_BAR_INTERVAL = 'F000AA43-0451-4000-B000-000000000000' # interval in tens of milliseconds

UUID_OPT_DATA     = 'F000AA71-0451-4000-B000-000000000000'
UUID_OPT_ENABLE   = 'F000AA72-0451-4000-B000-000000000000' # 0 to disable 1 to enable
UUID_OPT_INTERVAL = 'F000AA73-0451-4000-B000-000000000000' # interval in tens of milliseconds

def shortAtOffset(data, offset):
    lowerByte = data[offset] & 0xFF
    upperByte = data[offset + 1] & 0xFF
    return (upperByte << 8) + lowerByte

def extractTargetTemperatureTMP007(data):
    return shortAtOffset(data, 0) / 128.0

def extractLumens(data):
    rawData = shortAtOffset(data, 0)
    m = rawData & 0x0FFF
    e = (rawData & 0xF000) >> 12
    return m * (0.01 * 2 ** e)
    
class SensorTag(Sensor):
    def __init__(self, id, macAddr):
        Sensor.__init__(self, id, macAddr, 'CC2650 SensorTag')
    
    def onTempDataReceive(self, handle, data):
        temp = extractTargetTemperatureTMP007(data)
        sensorDump(self.deviceID, 'Temperature', temp)
    
    def onOpticalDataReceive(self, handle, data):
        lum = extractLumens(data)
        sensorDump(self.deviceID, 'Optical', lum)
    
    def connect(self, adapter):
        logging.info('connecting to %s...' % self.MAC)
        self.device = adapter.connect(self.MAC)
        logging.info('setting update interval to 10mins...')
        self.device.char_write_handle(self.device.get_handle(UUID_IRT_INTERVAL), [0xFF])
        logging.info('enabling IRT sensor...')
        self.device.char_write_handle(self.device.get_handle(UUID_IRT_ENABLE), [0x01])
        logging.info('subscribing to IRT sensor data...')
        self.device.subscribe(UUID_IRT_DATA, self.onTempDataReceive) 
        logging.info('setting update interval to 10mins...')
        self.device.char_write_handle(self.device.get_handle(UUID_OPT_INTERVAL), [0xFF])
        logging.info('enabling Optical sensor...')
        self.device.char_write_handle(self.device.get_handle(UUID_OPT_ENABLE), [0x01])
        logging.info('subscribing to Optical sensor...')
        self.device.subscribe(UUID_OPT_DATA, self.onOpticalDataReceive)
        return self.device
