import time
import datetime
import logging
import platform
from random import randint
from twython import Twython 

def __sensorDump_linux(device, metric, value):
    logging.info('Database write to %s from device %s: %s' % (metric, device, value))
    epoch = time.time()
    timestamp = datetime.datetime.fromtimestamp(epoch).strftime('%Y%W')
    epoch = (epoch - 14400) * 1000

    db = MySQLdb.connect('localhost', 'root', 'Z3r0Bas3', 'status')
    cursor = db.cursor()
    create = "CREATE TABLE sensorDump" + str(timestamp) + " SELECT * FROM sensorDump"
    insert = "INSERT INTO sensorDump" + str(timestamp) + " VALUES (" + str(device) + ", '" + str(metric) + "', " + str(epoch) + ", " + str(value) + ")"
   
    try:
        cursor.execute(create)
        db.commit()
    except:
        db.rollback()

    try:
        cursor.execute(insert)
        db.commit()
    except:
        db.rollback()        
    db.close()

def __toggle_linux(id, value):
    logging.info('Updating toggle %s to %s' % (id, value))
    update = "UPDATE Toggles SET Value=" + str(value) + " WHERE id='" + str(id) + "'"
    #if str(id) == 'doorOpen' and str(value) == '0':
    #    epoch = time.time()
    #    timestamp = datetime.datetime.fromtimestamp(epoch).strftime('%Y-%m-%d %H:%M:%S')
    #    key         = 'mGnvnyJAzLiqqVxO0I6q8AGjb'
    #    keySecret   = 'vB13zP99gyG9IPpSwi8R0B4ENKldK67X52gBtq0ZXZSYNXyNkA'
    #    token       = '4784036658-6xB18fpDOipWp47WuVIxdKgwb4Vgn0upucXQGPx'
    #    tokenSecret = 'i1We2gwCssO7PiRb85KETqpNEQRpL7vJlYOcD5KkiQJlB'
    #    twitter = Twython(key,keySecret,token,tokenSecret)
    #    twitter.update_status(status=str(timestamp)+' Toggle '+str(id)+"="+str(value))
    db = MySQLdb.connect('localhost', 'root', 'Z3r0Bas3', 'status')
    cursor = db.cursor()
    try:
        cursor.execute(update)
        db.commit()
    except:
        db.rollback()
        logging.error('Update failed!')
    db.close()

def __sensorDump_windows(device, metric, value):
    logging.info('Updating toggle %s to %s' % (id, value))

def __toggle_windows(id, value):
    logging.info('Updating toggle %s to %s' % (id, value))
    
# detect OS
sensorDump = None
toggle = None
if platform.system() == 'Windows':
    sensorDump = __sensorDump_windows
    toggle = __toggle_windows
elif platform.system() == 'Linux':
    import MySQLdb
    sensorDump = __sensorDump_linux
    toggle = __toggle_linux
