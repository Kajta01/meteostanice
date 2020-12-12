# Complete project details at https://RandomNerdTutorials.com
try:
  import usocket as socket
except:
  import socket


s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.bind(('', 80))
s.settimeout(0.15)
s.listen(1)

def web_page():

  
  html = """<html><head>
   <title>ESP Web Server</title> 
   <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="data:,"> 
  <style>html{font-family: Helvetica; display:inline-block; margin: 0px auto; text-align: center;}
  h1{color: #0F3376; padding: 2vh;}p{font-size: 1.5rem;}.button{display: inline-block; background-color: #e7bd3b; border: none; 
  border-radius: 4px; color: white; padding: 16px 40px; text-decoration: none; font-size: 30px; margin: 2px; cursor: pointer;}
  .button2{background-color: #4286f4;}</style>
  </head><body> <h1>ESP Web Server</h1> 
  <p><a href="/?led=white"><button class="button">White</button></a></p>
  <p><a href="/?led=slowR"><button class="button button2">Slow random</button></a></p>
  <p><a href="/?led=random"><button class="button button2">Random</button></a></p>
  <p><a href="/?led=color"><button class="button button2">Color</button></a></p>
  <p><a href="/?led=slowColor"><button class="button button2">Slow color</button></a></p>
  <p><a href="/?led=exit"><button class="button">Exit</button></a></p>
  </body></html>"""
  return html


def run():
  conn, addr = s.accept()
  print('Got a connection from %s' % str(addr))
  request = conn.recv(1024)
  request = str(request)
  print('Content = %s' % request)

  led_on = 2

  if(2==3):
    print("aa")

  if(6==((request).find('/?led=white'))):
    led_on = 2
  if(6==((request).find('/?led=random'))):
    led_on = 3
  if(6==((request).find('/?led=slowR'))):
    led_on = 4
  if(6==((request).find('/?led=color'))):
    led_on = 5
  if(6==((request).find('/?led=slowColor'))):
    led_on = 6

  if(6==((request).find('/?led=exit'))):
    led_on = 100
  

  response = web_page()
  conn.send('HTTP/1.1 200 OK\n')
  conn.send('Content-Type: text/html\n')
  conn.send('Connection: close\n\n')
  conn.sendall(response)
  conn.close()
  return led_on