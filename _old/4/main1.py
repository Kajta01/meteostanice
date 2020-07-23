import a11readDS as DS
import a12readAM as AM
import a13readBMP as BMP

import time
import machine

led = machine.Pin(16, machine.Pin.OUT)

print("New")
led.value(0)
time.sleep(1)
led.value(1)
time.sleep(1)

print("DS Teplota",DS.TemplotaDS())
print("AM Teplota", AM.Teplota(), "Vlhkost", AM.Vlhkost())
print("BMP Teplota", BMP.Teplota(), "Tlak", BMP.Tlak())
led.value(0)
