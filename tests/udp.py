#!/usr/bin/python

import fileinput

import socket
#UDP_IP = "192.168.1.7"
UDP_IP = "127.0.0.1"
UDP_PORT = 9696
MESSAGE = "Hello, World!"

sock = socket.socket( socket.AF_INET, socket.SOCK_DGRAM ) 
sock.sendto( MESSAGE , ( UDP_IP , UDP_PORT ) )

print( UDP_IP + ":" + str( UDP_PORT ) )

for rline in fileinput.input( ) :
    line = rline.strip( )
    #print( line )
    sock.sendto( line , ( UDP_IP , UDP_PORT ) )
