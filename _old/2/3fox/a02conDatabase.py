import time
import urequests


def AMTeplotaVlhkost(teplota,vlhkost):
    link = "http://foxhomes.tk/service/serviceAM.php?teplota=" + str(teplota) + "&vlhkost="+ str(vlhkost)
    print(link)
    f = urequests.get(link)
