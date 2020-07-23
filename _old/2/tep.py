TEMP=open("/sys/class/thermal/thermal_zone0/temp","r")
tt = float(TEMP.read())/1000

import MySQLdb

connection = MySQLdb.connect (host = "localhost",
                              user = "leccos",
                              passwd = "M08DEK7f",
                              db = "teplotaProcesoru")


cursor = connection.cursor()
val = (tt,"baf")
sql="INSERT INTO teplota (teplota,note) VALUES (%s,%s)"

cursor.execute(sql,val)

connection.commit()
cursor.close()
connection.close()

