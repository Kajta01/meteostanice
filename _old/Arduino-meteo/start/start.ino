// LCD displej pres I2C
// navody.arduino-shop.cz

// knihovny pro LCD přes I2C
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <OneWire.h>
#include <DallasTemperature.h>
#include <AM2320.h>
AM2320 senzor;
// nastavení adresy I2C (0x27 v mém případě),
// a dále počtu znaků a řádků LCD, zde 20x4
LiquidCrystal_I2C lcd(0x27, 16, 2);
const int pinCidlaDS = 2;
// vytvoření instance oneWireDS z knihovny OneWire
OneWire oneWireDS(pinCidlaDS);
// vytvoření instance senzoryDS z knihovny DallasTemperature
DallasTemperature senzoryDS(&oneWireDS);
void setup()
{
  // inicializace LCD
  lcd.begin();
  // zapnutí podsvícení
  lcd.noBacklight();
  lcd.backlight();

  // komunikace přes sériovou linku rychlostí 9600 baud
  Serial.begin(9600);
  // zapnutí komunikace knihovny s teplotním čidlem
  senzoryDS.begin();

}

void loop()
{
  Hluk();
  Vypis(0,0,"T:"+String(AM_Teplota())+" V:"+String(AM_Vlhkost()));
  Vypis(0,1,"Slusi ti to!");
  
}
void Vypis(int sl, int ra, String hodnota)
{
  lcd.setCursor ( sl, ra );
  lcd.print(hodnota);
delay(500);
  }
double AM_Teplota()
{
  // načtení stavu připojeného senzoru
  switch (senzor.Read()) {
    // v případě stavu "2" je chyba komunikace
    case 2:
      Serial.println("Chybny CRC soucet, chyba v komunikaci!");
      break;
    // v případě stav "1" je senzor offline nebo špatně připojen
    case 1:
      Serial.println("Senzor offline!");
      break;
    // v případě stavu "0" je vše v pořádku
    // a můžeme vytisknout údaje
    case 0:
      return senzor.t;
  } 
  }
  double AM_Vlhkost()
{
  // načtení stavu připojeného senzoru
  switch (senzor.Read()) {
    // v případě stavu "2" je chyba komunikace
    case 2:
      Serial.println("Chybny CRC soucet, chyba v komunikaci!");
      break;
    // v případě stav "1" je senzor offline nebo špatně připojen
    case 1:
      Serial.println("Senzor offline!");
      break;
    // v případě stavu "0" je vše v pořádku
    // a můžeme vytisknout údaje
    case 0:
      return senzor.h;
  }
  }
 void DS()
 {
      // načtení informací ze všech připojených čidel na daném pinu
  senzoryDS.requestTemperatures();
  // výpis teploty na sériovou linku, při připojení více čidel
  // na jeden pin můžeme postupně načíst všechny teploty
  // pomocí změny čísla v závorce (0) - pořadí dle unikátní adresy čidel
  Serial.print("Teplota cidla DS18B20: ");
  Serial.print(senzoryDS.getTempCByIndex(0));
  Serial.println(" stupnu Celsia");
  // pauza pro přehlednější výpis
  delay(2000);
  
  }
  void Hluk()
  {
    const int SenOut = 13;
    pinMode(SenOut, INPUT);
 
    int p = digitalRead(SenOut);
    delay(20);
    int d = digitalRead(SenOut);
    delay(20);
    int t = digitalRead(SenOut);
    delay(20);
    int c = digitalRead(SenOut);
    delay(20);
    int f = digitalRead(SenOut);
    int sum = p+d+t+c+f;
Serial.println(String(p)+String(d)+String(t)+String(c)+String(f)+String(sum));
  if(sum < 4)
  {
    lcd.backlight();
    }
    else
    {
      lcd.noBacklight();
      }
  
  }


