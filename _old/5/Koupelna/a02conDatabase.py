import time
import urequests


def DataTV(mistnost,teplota,vlhkost):
    link = "http://foxhomes.tk/_/service/service.php?tabulka="+mistnost+"&teplota=" + str(teplota) + "&vlhkost="+ str(vlhkost)
    print(link)
    f = urequests.get(link)
    f.close()
def Data(mistnost,teplota):
    link = "http://foxhomes.tk/_/service/service.php?tabulka="+mistnost+"&teplota=" + str(teplota)
    print(link)
    f = urequests.get(link)
    f.close()
