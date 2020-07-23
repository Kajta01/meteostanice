import max30102
m = max30102.ParticleSensor() # sensor initialization
red, ir = m.read_sequential() # get LEDs readings
print(m)
