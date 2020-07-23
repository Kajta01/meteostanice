import dht
from machine import Pin
sensor = dht.DHT22(Pin(14))
def Vlhkost():
    sensor.measure()
    hum = (sensor.humidity()/12)
    return hum