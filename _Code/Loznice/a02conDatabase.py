import time
import urequests


def Data(mistnost,teplota, vlhkost, tlak, kvetina1):
    link = "https://foxhomes.000webhostapp.com/service/service.php?tabulka="+mistnost+"&teplota=" + str(teplota)+"&vlhkost=" + str(vlhkost)+"&tlak=" + str(tlak)+"&kvetina1=" + str(kvetina1)
    print(link)
    f = urequests.get(link)
    f.close()


