
import hypermedia.net.*;

UDP udp ;


PImage img;
int[][] imgPixels;
float sval = 1.0;
float nmx, nmy;
int res = 5;



void setup() {

  // create a new datagram connection on port 6000
  // and wait for incomming message
  udp = new UDP( this, 9696 );
  //udp.log( true ); 		// <-- printout the connection activity
  udp.listen( true );


  size(640, 360, P3D);
  noFill();
  stroke(255);
  img = loadImage("/home/zaf/Pictures/face.jpeg");
  imgPixels = new int[img.width][img.height];
  for (int i = 0; i < img.height; i++) {
    for (int j = 0; j < img.width; j++) {
      imgPixels[j][i] = img.get(j, i);
    }
  }


}

int x1=0;
int y1=0;
int x2=0;
int y2=0;

//process events
void draw() {

  background(0);

  //nmx += (mouseX-nmx)/20; 
  //nmy += (mouseY-nmy)/20; 

  nmx += (x1-nmx)/20; 
  nmy += (y1-nmy)/20; 

  sval=1+(x2/100.0);


  sval = constrain(sval, 1.0, 2.0);

  translate(width/2 + nmx * sval-100, height/2 + nmy*sval - 100, -50);
  scale(sval);
  rotateZ(PI/9 - sval + 1.0);
  rotateX(PI/sval/8 - 0.125);
  rotateY(sval/8 - 0.125);

  translate(-width/2, -height/2, 0);

  for (int i = 0; i < img.height; i += res) {
    for (int j = 0; j < img.width; j += res) {
      float rr = red(imgPixels[j][i]); 
      float gg = green(imgPixels[j][i]);
      float bb = blue(imgPixels[j][i]);
      float tt = rr+gg+bb;
      stroke(rr, gg, gg);
      line(i, j, tt/10-20, i, j, tt/10 );
    }
  }

 

  
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