import a03readDS as DS
import a04readAM as AM
import a02conDatabase as DTB
import a01conectWifi as WF
import time
import machine

led = machine.Pin(0, machine.Pin.OUT)
WF.connect()
while True:
    print("New")
    led.value(1)
    time.sleep(1)
    led.value(0)
    time.sleep(1)
    led.value(1)
    try:
        print("e")
        if WF.isconnected() == False:
            WF.connect()
        print("ee")
        DTB.Teplota(DS.TemplotaDS())

        DTB.Vlhkost(AM.Vlhkost())
        led.value(0)
    except:
        print("An exception occurred")
    print("Sl")
    time.sleep(300)
    print("eep")
    print("Re")
    machine.reset()
