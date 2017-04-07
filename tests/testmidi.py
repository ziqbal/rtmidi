import pygame
import pygame.midi
import commands
import sys
import os
import socket

pygame.midi.init( )
if pygame.midi.get_count( ) < 3 :
    sys.exit( )

#UDP_IP = "192.168.1.2"
UDP_IP = "127.0.0.1"
UDP_PORT = 9696
MESSAGE = "Hello, World!"

sock = socket.socket( socket.AF_INET, socket.SOCK_DGRAM ) 
sock.sendto( MESSAGE , ( UDP_IP , UDP_PORT ) )

print( UDP_IP + ":" + str( UDP_PORT ) )

for x in range( 0 , pygame.midi.get_count( ) ) :
    print pygame.midi.get_device_info( x )

inp = pygame.midi.Input( 3 )

usbMonitor = commands.getoutput( "dmesg | grep -i hercules" )

frame = 0

msgc = 1

controlRightWheelX = 0
controlRightWheelXPlus = [176,51,1,0]
controlRightWheelXMinus = [176,51,127,0] 
controlLeftWheelX = 0
controlLeftWheelXPlus = [176,50,1,0]
controlLeftWheelXMinus = [176,50,127,0] 

controlRightWheel = 0
controlRightWheelPlus = [176,49,1,0]
controlRightWheelMinus = [176,49,127,0] 
controlLeftWheel = 0
controlLeftWheelPlus = [176,48,1,0]
controlLeftWheelMinus = [176,48,127,0] 

controlRightPitch = 0
controlRightPitchPlus = [176,53,1,0]
controlRightPitchMinus = [176,53,127,0]
controlLeftPitch = 0
controlLeftPitchPlus = [176,52,1,0]
controlLeftPitchMinus = [176,52,127,0]
 
while True :
    if inp.poll( ) :
        messages = inp.read( 1000 )
        for message in messages :
            mk = message[ 0 ]
            mt = message[ 1 ]

            cc = 0 

            if( mk == controlRightWheelPlus ) :
                controlRightWheel=controlRightWheel+1
                cc = controlRightWheel
            if( mk == controlRightWheelMinus) :
                controlRightWheel=controlRightWheel-1
                cc = controlRightWheel
            if( mk == controlLeftWheelPlus ) :
                controlLeftWheel=controlLeftWheel+1
                cc = controlLeftWheel
            if( mk == controlLeftWheelMinus) :
                controlLeftWheel=controlLeftWheel-1
                cc = controlLeftWheel

            if( mk == controlRightWheelXPlus ) :
                controlRightWheelX=controlRightWheelX+1
                cc = controlRightWheelX
            if( mk == controlRightWheelXMinus) :
                controlRightWheelX=controlRightWheelX-1
                cc = controlRightWheelX
            if( mk == controlLeftWheelXPlus ) :
                controlLeftWheelX=controlLeftWheelX+1
                cc = controlLeftWheelX
            if( mk == controlLeftWheelXMinus) :
                controlLeftWheelX=controlLeftWheelX-1
                cc = controlLeftWheelX

            if( mk == controlRightPitchPlus ) :
                controlRightPitch=controlRightPitch+1
                cc = controlRightPitch
            if( mk == controlRightPitchMinus ) :
                controlRightPitch=controlRightPitch-1
                cc = controlRightPitch
            if( mk == controlLeftPitchPlus ) :
                controlLeftPitch=controlLeftPitch+1
                cc = controlLeftPitch
            if( mk == controlLeftPitchMinus ) :
                controlLeftPitch=controlLeftPitch-1
                cc = controlLeftPitch

            line = str( mt ) + ","+str(msgc)+"," + str( mk[ 0 ] ) + "," + str( mk[ 1 ] ) + "," + str( mk[ 2 ] ) + ","+str(cc) 
            #print(message)
            print( line )
            sock.sendto( line , ( UDP_IP , UDP_PORT ) ) 
            msgc=msgc+1

    frame = frame + 1

    if frame % 1000 == 0 :
        if usbMonitor != commands.getoutput( "dmesg | grep -i hercules" ) :
            #inp.close()
            pygame.midi.quit( )
            #pygame.quit( )
            #del inp
            os.system( "kill $PPID" )
            #sys.exit( )

    pygame.time.wait( 1 )