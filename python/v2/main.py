import a03readDS as DS
import a04readAM as AM
import a02conDatabase as DTB
import a01conectWifi as WF
import time
import machine


while True:
    try:
        WF.connect()
        DTB.jenTeplota(DS.TemplotaDS())

        DTB.TeplotaVlhkost(AM.Teplota(),AM.Vlhkost())
        time.sleep(600)
    except:
        print("An exception occurred")
        machine.restart()
