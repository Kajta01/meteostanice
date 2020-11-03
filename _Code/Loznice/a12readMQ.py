from MQ7 import MQ7

import time




def Run():
	time.sleep(5)
	sensor = MQ7(pinData = 0, baseVoltage = 3.3)
	print("Calibrating")
	sensor.calibrate()
	print("Calibration completed")
	print("Base resistance:{0}".format(sensor._ro))
	return  sensor.readCarbonMonoxide()

