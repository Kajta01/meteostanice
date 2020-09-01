import time
import urequests


def Data(mistnost,teplota,kyslik, voda):
    link = "http://foxhomes.ga.srv11.endora.cz/_/service/service.php?tabulka="+mistnost+"&teplota=" + str(teplota)+"&voda=" + str(voda)+"&kyslik=" + str(kyslik)
    print(link)
    f = urequests.get(link)
    f.close()
