/**
 * (./) udp.pde - how to use UDP library as unicast connection
 * (cc) 2006, Cousot stephane for The Atelier Hypermedia
 * (->) http://hypermedia.loeil.org/processing/
 *
 * Create a communication between Processing<->Pure Data @ http://puredata.info/
 * This program also requires to run a small program on Pd to exchange data  
 * (hum!!! for a complete experimentation), you can find the related Pd patch
 * at http://hypermedia.loeil.org/processing/udp.pd
 * 
 * -- note that all Pd input/output messages are completed with the characters 
 * ";\n". Don't refer to this notation for a normal use. --
 */

// import UDP library
import hypermedia.net.*;


UDP udp;  // define the UDP object

/**
 * init
 */
void setup() {

  // create a new datagram connection on port 6000
  // and wait for incomming message
  udp = new UDP( this, 9696 );
  //udp.log( true ); 		// <-- printout the connection activity
  udp.listen( true );
   size(640, 360);
  background(102);
}

int x1=0;
int y1=0;
int x2=0;
int y2=0;

//process events
void draw() {
 stroke(255);
    clear();
    ellipse(x1,y1, x2,y2);


  
}

/** 
 * on key pressed event:
 * send the current key value over the network
 */
void keyPressed() {
    
    String message  = str( key );	// the message to send
    String ip       = "localhost";	// the remote IP address
    int port        = 6100;		// the destination port
    
    // formats the message for Pd
    message = message+";\n";
    // send the message
    udp.send( message, ip, port );
    
}

/**
 * To perform any action on datagram reception, you need to implement this 
 * handler in your code. This method will be automatically called by the UDP 
 * object each time he receive a nonnull message.
 * By default, this method have just one argument (the received message as 
 * byte[] array), but in addition, two arguments (representing in order the 
 * sender IP address and his port) can be set like below.
 */
// void receive( byte[] data ) { 			// <-- default handler
void receive( byte[] data, String ip, int port ) {	// <-- extended handler
  
  
  // get the "real" message =
  // forget the ";\n" at the end <-- !!! only for a communication with Pd !!!
  //data = subset(data, 0, data.length-2);
  String message = new String( data );
  String[] list = split(message, ',');
  
  if(list.length!=6){
    return;
    
  }
  // print the result
  //println( "receive: \""+message+"\" from "+ip+" on port "+port );
   
   int k1 = int(list[2]);
   int k2 = int(list[3]);
   int k3 = int(list[4]);
   
   if(k1==176 && k2==49 && ( k3==1 || k3==127 ) ){
     x1=int(list[5]);
   }
   if(k1==176 && k2==48 && ( k3==1 || k3==127 )){
     y1=int(list[5]);
   }   
   
   if(k1==176 && k2==51 && ( k3==1 || k3==127 ) ){
     x2=int(list[5]);
   }
   if(k1==176 && k2==50 && ( k3==1 || k3==127 )){
     y2=int(list[5]);
   }   
   
   //x1=int(list[5]);
  //line(int(list[5]),100,200,200);
}