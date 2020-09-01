import dht
from machine import Pin
sensor = dht.DHT11(Pin(14))
def Vlhkost():
    sensor.measure()
    hum = (sensor.humidity())
    return hum

