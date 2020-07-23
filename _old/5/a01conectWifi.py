import network
def connect():

    ssid = "Kajta"
    password =  "***M08DEK7f***"

    station = network.WLAN(network.STA_IF)

    if station.isconnected() == True:
        print("Already connected")
        return

    station.active(True)
    station.connect(ssid, password)

    while station.isconnected() == False:
        pass

    print("Connection successful")
    print(station.ifconfig())
    ap_if = network.WLAN(network.AP_IF)
    ap_if.active(False)
def isconnected():
    return station.isconnected()
