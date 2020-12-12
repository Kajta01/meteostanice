import a01server
from light import Light

pinON = Pin(15, Pin.OUT)

lightValue = 2

l = Light()


while True:
    try:
        lightValue = a01server.run()
        print("New val")
 

    except:        
        if(lightValue == 2):
            l.white()
            Iret = 1
        if(lightValue == 3):
            l.random()
            Iret = 1
        if(lightValue == 4): 
            l.randomSlow()
        if(lightValue == 5): 
            l.color()
        if(lightValue == 6): 
            l.colorSlow()
        if(lightValue == 100):
            break



        print(lightValue)
 
    