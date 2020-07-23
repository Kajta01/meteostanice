import time
import network
import machine
ssid = "Ubytovani"
password =  "UbytovaniWIFI_2013"

station = network.WLAN(network.STA_IF)
def connect():
    print("Connecting......")




    station.active(True)
    if station.isconnected() == True:
        print("Already connected")
        return
    if not station.isconnected():
        station.connect()
        print("Waiting for connection...")
        temp = 0
        while not station.isconnected():
            temp = temp + 1
            time.sleep(2)

            print("Waiting for connection...")
            if temp == 10:
                print("Reset")
                machine.reset()
    print("Connection successful")
def isconnected():
    return station.isconnected()
