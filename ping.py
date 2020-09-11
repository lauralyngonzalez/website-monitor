'''
Pings a host address continuously based on the given DELAY.
Results are written to a file and the user is notified via
email when the server goes down and when the server comes back up.
'''

import subprocess
import time
import smtplib, ssl, getpass

DELAY = 5
notified = False

'''
Sends one packet to a given host. Returns the result.
0 is successful, anything else is an error.
'''
def ping(host):
    res = subprocess.call(['ping', '-c', '1', address], stdout = file)
    #res = subprocess.call(['ping', '-i', '3', address])
    #res = subprocess.call(['ping', address])
    return res

'''
If there is an error, the receiver is notified via email.
'''
def notify(password, status_str):
    sender_email = "my@gmail.com"
    receiver_email = "you@gmail.com"
    message = """\
    Subject: Hi there """ + status_str + """
    
    This message is sent from Python."""
    #password = input("Type your password and press enter: ")

    # Create a secure SSL context
    context = ssl.create_default_context()

    # probably shouldn't check credentials this late...
    with smtplib.SMTP_SSL("smtp.gmail.com", port, context=context) as server:
        server.login("you@gmail.com", password)
        server.sendmail(sender_email, receiver_email, message)

address = "159.65.77.37"
timestr = time.strftime("%Y%m%d_%H%M%S")
file = open(timestr + ".txt", "a")

port = 465  # For SSL
password = getpass.getpass(prompt="Type your password and press enter: ")

while True:
    res = ping(address)
    if res == 0:
        file.write("ping to " + address + " OK\n")
        if (notified): # notify if the server comes back up
            notify(password, address + " is up!")
            notified = False
    else:
        file.write("ping to " + address + " failed!\n")
        if (not notified): #notify if the server goes down
            notify(password, address + " is down!")
            notified = True
    time.sleep(DELAY) 

file.close()

