from machine import Pin
from neopixel import NeoPixel
import urandom
import time

PRECHOD = 50

POCET_LED = 8
pin = Pin(13, Pin.OUT)
np = NeoPixel(pin, POCET_LED)

def sendLightVal(r,g,b):
    print(r,g,b)
    for x in range(POCET_LED):
        np[x] = (r, g, b)
        np.write()

class Light:

    
    iret = 0

    r=0
    g=0
    b=0

    newR = 0
    newG = 0
    newB = 0

    def white(self):  
        self.r = 255
        self.g = 255
        self.b = 255
        sendLightVal(self.r,self.g,self.b)

    def random(self):
        self.r = urandom.getrandbits(8)
        self.g = urandom.getrandbits(8)
        self.b = urandom.getrandbits(8)
        sendLightVal(self.r,self.g,self.b)

    def randomSlow(self):
        if(self.iret == 0):
            self.newR = urandom.getrandbits(8)
            self.newG = urandom.getrandbits(8)
            self.newB = urandom.getrandbits(8)

        print(self.r, self.g, self.b, " - ",self.newR,self.newG,self.newB)
        self.iret = self.iret + 1 
        print(self.iret)


        rozdilR = self.newR - self.r
        rozdilG = self.newG - self.g
        rozdilB = self.newB - self.b

        print("Rozdil:",rozdilR, rozdilG, rozdilB)

        meziR = (rozdilR/PRECHOD)
        meziG = (rozdilG/PRECHOD)
        meziB = (rozdilB/PRECHOD)

        print("Mezi:",meziR, meziG, meziB)

        meziR = (rozdilR/PRECHOD)*self.iret
        meziG = (rozdilG/PRECHOD)*self.iret
        meziB = (rozdilB/PRECHOD)*self.iret

        print("Mezi2:",meziR, meziG, meziB)

        sendLightVal(
        (int)(self.r+(meziR)),
        (int)(self.g+(meziG)),
        (int)(self.b+(meziB)))

        if(self.iret >= PRECHOD):
            self.iret = 0
            self.r = self.newR
            self.g = self.newG
            self.b = self.newB

    def color(self):
        if(self.iret == 8):
            self.iret = 0
        if(self.iret == 0):
            sendLightVal(0,0,0)
        if(self.iret == 1):
            sendLightVal(0,0,255)
        if(self.iret == 2):
            sendLightVal(0,255,0)
        if(self.iret == 3):
            sendLightVal(255,0,0)
        if(self.iret == 4):
            sendLightVal(255,255,0)
        if(self.iret == 5):
            sendLightVal(0,255,255)
        if(self.iret == 6):
            sendLightVal(255,0,255)
        if(self.iret == 7):
            sendLightVal(255,255,255)
        self.iret = self.iret + 1
        
    def color(self):
        if(self.iret == 8):
            self.iret = 0
        if(self.iret == 0):
            sendLightVal(0,0,0)
        if(self.iret == 1):
            sendLightVal(0,0,255)
        if(self.iret == 2):
            sendLightVal(0,255,0)
        if(self.iret == 3):
            sendLightVal(255,0,0)
        if(self.iret == 4):
            sendLightVal(255,255,0)
        if(self.iret == 5):
            sendLightVal(0,255,255)
        if(self.iret == 6):
            sendLightVal(255,0,255)
        if(self.iret == 7):
            sendLightVal(255,255,255)
        self.iret = self.iret + 1




      