from time import sleep
from machine import Pin
import onewire
from ds18x20 import DS18X20

def TemplotaDS():
    pin = Pin(2, Pin.IN)
    ow = DS18X20(onewire.OneWire(pin))

    sensory = ow.scan()
    ow.convert_temp()
    sleep(1)
    teplota = ow.read_temp(sensory[0])
    return teplota
