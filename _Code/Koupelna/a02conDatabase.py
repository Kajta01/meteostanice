import time
import urequests


def DataTV(mistnost,teplota,vlhkost):
    link = "https://foxhomes.000webhostapp.com//service/service.php?tabulka="+mistnost+"&teplota=" + str(teplota) + "&vlhkost="+ str(vlhkost)
    print(link)
    f = urequests.get(link)
    f.close()
def Data(mistnost,teplota):
    link = "https://foxhomes.000webhostapp.com/service/service.php?tabulka="+mistnost+"&teplota=" + str(teplota)
    print(link)
    f = urequests.get(link)
    f.close()
