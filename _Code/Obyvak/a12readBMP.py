from bmp180 import BMP180
from machine import I2C, Pin                        # create an I2C bus object accordingly to the port you are using
bus = I2C(scl=Pin(12), sda=Pin(14))  # on esp8266
bmp180 = BMP180(bus)
bmp180.oversample_sett = 2
bmp180.baseline = 101325

def Tlak():

    return bmp180.pressure

def NadMVyska():    
    return bmp180.altitude
