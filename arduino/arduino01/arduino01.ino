void setup(){
Serial.begin(9600);
while(true)
{
int k = random(10,30);
Serial.println(String(k));
delay(80000); 
}
}

