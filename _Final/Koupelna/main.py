
from time import sleep

import machine
from machine import Pin

pin = Pin(14, Pin.OUT)
while True:
  pin.value(0)
  sleep(1/2)
  pin.value(1)
  sleep(1/2)

  pin.value(1)
