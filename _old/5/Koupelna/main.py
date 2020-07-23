
import a03readDS as DS
import a04readAM as AM
import a02conDatabase as DTB
import a01conectWifi as WF
import time
from machine import Pin
import machine

def deep_sleep(msecs):
  # configure RTC.ALARM0 to be able to wake the device
  rtc = machine.RTC()
  rtc.irq(trigger=rtc.ALARM0, wake=machine.DEEPSLEEP)

  # set RTC.ALARM0 to fire after X milliseconds (waking the device)
  rtc.alarm(rtc.ALARM0, msecs)

  # put the device to sleep
  machine.deepsleep()

pin = Pin(14, Pin.OUT)
try:

    WF.connect()

    pin.value(1)
    #DTB.Data("Koupelna",DS.TeplotaDS());
    #time.sleep(2)
    DTB.DataTV("Koupelna",AM.Teplota(),AM.Vlhkost());
    time.sleep(1)
    pin.value(0)
    time.sleep(5)
    deep_sleep(600000)
except:
    pin.value(0)
    machine.reset()
