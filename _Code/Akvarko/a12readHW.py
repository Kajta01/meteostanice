from machine import Pin, ADC
from time import sleep

pin = Pin(12, Pin.OUT)
def Voda():
    pin.value(1)
    sleep(2)
    pot = ADC(0)
    voda = pot.read()
    pin.value(0)
    return voda



  
