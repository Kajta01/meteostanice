import time
import urequests


def jenTeplota(teplota):
    link = "http://www.foxhomes.ga/service.php?teplota=" + str(teplota)
    print(link)
    f = urequests.get(link)

def TeplotaVlhkost(teplota,vlhkost):
    link = "http://www.foxhomes.ga/serviceAM.php?teplota=" + str(teplota) + "&vlhkost=" + str(vlhkost)
    print(link)
    f = urequests.get(link)
