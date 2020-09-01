import am2320
from machine import I2C, Pin
i2c = I2C(scl=Pin(12), sda=Pin(14))
sensor = am2320.AM2320(i2c)

def Teplota():
    sensor.measure()
    return sensor.temperature()

def Vlhkost():
    sensor.measure()
    return sensor.humidity()

