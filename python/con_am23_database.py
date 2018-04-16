import serial
import time
import datetime
import requests
from apscheduler.schedulers.blocking import BlockingScheduler

def some_job():
    line = []
    for c in ser.readline().decode('utf-8'):
        if c == '\n':
            if len(line) != 0:
                teplota,vlhkost = joined_seq.split(' ');
                print('{:%Y-%m-%d %H:%M:%S} {} '.format(datetime.datetime.now(),teplota,' ',vlhkost))
                link = "http://www.foxhomes.kvalitne.cz/service.php?teplota=" + teplota + "&vlhkost=" + vlhkost

                print(link)
                f = requests.get(link)
                line = []
        else:
            line.append(c)
            joined_seq = ''.join(str(v) for v in line)

ser = serial.Serial(
    port='COM3',\
    baudrate=9600,\
    parity=serial.PARITY_NONE,\
    stopbits=serial.STOPBITS_ONE,\
    bytesize=serial.EIGHTBITS,\
        timeout=0)

print("connected to: " + ser.portstr)

scheduler = BlockingScheduler()
scheduler.add_job(some_job, 'interval', seconds=10)
scheduler.start()
#this will store the line
