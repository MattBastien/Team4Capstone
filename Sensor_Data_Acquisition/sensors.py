import pygatt.backends
from pygatt.exceptions import NotConnectedError
from pygatt.backends.bgapi.exceptions import ExpectedResponseTimeout
from time import sleep
import logging
import threading

class Sensor:
    def __init__(self, id, macAddr, name=None):
        self.deviceID = id
        self.MAC = macAddr
        self.name = name
        self.device = None
    
    def isConnected(self, adapter):
        if self.device == None:
            return False
        return self.device in adapter._connections.values()
    
    def compare(self, advertiser):
        # can be overriden on a per sensor basis
        if self.MAC > advertiser['address']:
            return 1
        elif self.MAC == advertiser['address']:
            return 0
        return -1

class SensorManager:
    def __init__(self):
        self.__sensor_list = []
    
    def __scan_thread(self):
        logging.info('starting the adapter...')
        adapter = pygatt.backends.BGAPIBackend()
        while self.__running:
            try:
                adapter.start()
            
                while self.__running:
                    # We need to do a 0s scan for pygatt to update its connections dictionary
                    scan = adapter.scan(timeout=0)
                    # Check sensor status to see if we even need to perform a scan
                    needScan = False
                    for sensor in self.__sensor_list:
                        if not sensor.isConnected(adapter):
                            needScan = True
                            sensor.device = None
                    if needScan:
                        logging.info('scanning for devices...')
                        scan = adapter.scan(timeout=2)
                        if len(scan) > 0:
                            logging.info('found advertising devices:')
                            for advertiser in scan:
                                logging.info('\t' + advertiser['name'] + ' ' + advertiser['address'])
                            for advertiser in scan:
                                for sensor in self.__sensor_list:
                                    if sensor.compare(advertiser) == 0:
                                        logging.info('Found sensor ' + advertiser['address'])
                                        sensor.connect(adapter)
                        else:
                            logging.info('found no advertising devices')
                    else:
                        sleep(0.5)
            except ExpectedResponseTimeout, NotConnectedError:
                logging.warning('Adapter failed to respond. Restarting...')
                adapter.stop()
    
    def isRunning(self):
        return self.__running
    
    def start(self):
        logging.info('Starting the scanning thread...')
        self.__running = True
        threading.Thread(name='Scanning Thread', target=self.__scan_thread).start()
    
    def stop(self):
        self.__running = False
    
    def addSensor(self, sensor):
        if sensor in self.__sensor_list:
            return
        self.__sensor_list.append(sensor)
    
    def removeSensor(self, sensor):
        self.__sensor_list.remove(sensor)
