from machine import Pin
import machine
import a11readAM as AM
import a12readBMP as BMP
import a13readSM as SM
import a01conectWifi as WF
import a02conDatabase as DTB
import time


def deep_sleep(msecs):
  # configure RTC.ALARM0 to be able to wake the device
  rtc = machine.RTC()
  rtc.irq(trigger=rtc.ALARM0, wake=machine.DEEPSLEEP)

  # set RTC.ALARM0 to fire after X milliseconds (waking the device)
  rtc.alarm(rtc.ALARM0, msecs)

  # put the device to sleep
  machine.deepsleep()

pin = Pin(5, Pin.OUT)

try:
    WF.connect()

    pin.value(1)

    time.sleep(2)
    DTB.Data("Obyvak",AM.Teplota(),AM.Vlhkost(),BMP.Tlak(),SM.Voda())
    time.sleep(2)

    pin.value(0)
    time.sleep(2)
    deep_sleep(600000) 

except:
    pin.value(0)
    machine.reset()

