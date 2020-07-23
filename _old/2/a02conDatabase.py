import time
import urequests


def Teplota(teplota):
    link = "http://192.168.2.81/serviceTep.php?hodnota=" + str(teplota)
    print(link)
    f = urequests.get(link)

def Vlhkost(vlhkost):
    link = "http://192.168.2.81/serviceVlh.php?hodnota=" + str(vlhkost)
    print(link)
    f = urequests.get(link)
