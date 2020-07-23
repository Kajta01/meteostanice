
import a04readAM as AM
import a02conDatabase as DTB
import a01conectWifi as WF
import time
import machine
import am2320
from machine import I2C, Pin

WF.connect()
while True:
    print("New")

    time.sleep(1)

    time.sleep(1)

    try:
        print("e")
        i2c = I2C(scl=Pin(12), sda=Pin(14))
        sensor = am2320.AM2320(i2c)
        sensor.measure()
        print(sensor.temperature())
        print(sensor.humidity())
        print("eee")
        DTB.AMTeplotaVlhkost(sensor.temperature(),sensor.humidity())
    except:
        print("An exception occurred")
    print("Sl")
    time.sleep(300)
    print("eep")
    print("Reset")
    machine.reset()
