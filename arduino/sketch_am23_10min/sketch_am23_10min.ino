// Teploměr a vlhkoměr AM2320

// připojení potřebných knihoven
#include <Wire.h>
#include <AM2320.h>
// inicializace modulu z knihovny
AM2320 senzor;

void setup() {
  // zahájení komunikace po sériové lince
  // rychlostí 9600 baud
  Serial.begin(9600);
}

void loop() {
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

      Serial.print(senzor.t);
      Serial.print(" ");
      Serial.println(senzor.h);

      break;
      }

  
  // pauza před novým měřením
  delay(600000);
}
