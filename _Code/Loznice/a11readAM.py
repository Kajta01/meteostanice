import am2320
from machine import I2C, Pin
import time
i2c = I2C(scl=Pin(12), sda=Pin(14))
sensor = am2320.AM2320(i2c)

pin = Pin(2, Pin.OUT)

def Teplota():
    pin.value(1)
    time.sleep(5)
    sensor.measure()  
    val = sensor.temperature()
    pin.value(0)

    return val

def Vlhkost():
    pin.value(1)
    time.sleep(5)
    sensor.measure()    
    val =  sensor.humidity()
    pin.value(0)
    return val
