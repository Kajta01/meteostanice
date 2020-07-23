import am2320
from machine import I2C, Pin
def Teplota():
    i2c = I2C(scl=Pin(12), sda=Pin(14))
    sensor = am2320.AM2320(i2c)
    sensor.measure()
    return sensor.temperature()
def Vlhkost():
    i2c = I2C(scl=Pin(12), sda=Pin(14))
    sensor = am2320.AM2320(i2c)
    sensor.measure()
    return sensor.humidity()

print(Teplota())
