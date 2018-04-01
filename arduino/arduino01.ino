void setup(){
Serial.begin(9600);
int k = 0;
while(true)
{
Serial.println(String(k));
k = k+1;
delay(80000); 
}
}

