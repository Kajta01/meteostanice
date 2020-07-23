import max30100
mx30 = max30100.MAX30100()
import time

mx30.set_mode(max30100.MODE_SPO2)

mx30.read_sensor()
print(mx30.ir, mx30.red)
print(mx30.buffer_red)
