#include "MAX30100.h"
#include <Wire.h>

MAX30100 sensor;

void setup() {
  Wire.begin();
  Serial.begin(115200);
  while(!Serial);
  sensor.begin(pw1600, i50, sr100 );
}

void loop() {
  sensor.readSensor();
  Serial.println(sensor.IR);
  delay(10);
}
