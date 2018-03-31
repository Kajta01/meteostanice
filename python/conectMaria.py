import mysql.connector as mariadb

mariadb_connection = mariadb.connect(host ='185.64.219.6', user='foxhomeskval6395', password='7Zyqp7Q', database='foxhomeskval6395')
cursor = mariadb_connection.cursor()

#cursor.execute("INSERT INTO Test1 (Datum,Cas,Hodnnota,Note) VALUES (%s,%s)", (first_name, last_name))

cursor.execute("INSERT INTO Test1 (Note) VALUES (%s)", ('python1'))
mariadb_connection.commit()
mariadb_connection.rollback()
