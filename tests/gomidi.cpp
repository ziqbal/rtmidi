
#include <iostream>
#include <cstdlib>
#include <typeinfo>
#include "RtMidi.h"

#include <sys/types.h>
#include <sys/socket.h>

#include <unistd.h>
#define SLEEP( milliseconds ) usleep( ( unsigned long ) ( milliseconds * 1000.0) )

bool chooseMidiPort( RtMidi *rtmidi ) ;

long long frame = 0 ;

void mycallback( double deltatime , std::vector< unsigned char > *message , void * ) {

    std::cout << ( int )message->at( 0 ) << "," ;
    std::cout << ( int )message->at( 1 ) << "," ;
    std::cout << ( int )message->at( 2 ) << "," ;
    std::cout << std::fixed << deltatime << std::endl ;

/*
    unsigned int nBytes = message->size( ) ;

    for ( unsigned int i = 0 ; i < nBytes ; i++ ) {
    
        std::cout << ( int )message->at( i ) << "," ;

    }

    if ( nBytes > 0 ) {

        std::cout << deltatime << std::endl ;

    }

*/

}

//int main( int argc, char *argv[] )
int main(  )
{

  RtMidiOut *midiout = 0;
  RtMidiIn *midiin = 0;
  std::vector<unsigned char> message;

  // RtMidiOut and RtMidiIn constructors
  try {
    midiout = new RtMidiOut();
    midiin = new RtMidiIn();
  }
  catch ( RtMidiError &error ) {
    error.printMessage();
    goto cleanup;
  }

  // Don't ignore sysex, timing, or active sensing messages.
  midiin->ignoreTypes( false, true, true );

  try {
    if ( chooseMidiPort( midiin ) == false ) goto cleanup;
    if ( chooseMidiPort( midiout ) == false ) goto cleanup;
  }
  catch ( RtMidiError &error ) {
    error.printMessage();
    goto cleanup;
  }

  midiin->setCallback( &mycallback );

  message.push_back( 0xF6 );
  midiout->sendMessage( &message );

  //SLEEP( 60000 ); 

  while( true ) {

    SLEEP( 10000 ) ;

  }

  // Clean up
 cleanup:
  delete midiout;
  delete midiin;

  //std::cout << "SHUTDOWN\n" ;

  return 0;
}

bool chooseMidiPort( RtMidi *rtmidi )
{
  bool isInput = false;
  if ( typeid( *rtmidi ) == typeid( RtMidiIn ) )
    isInput = true;


  std::string portName;
  unsigned int i = 0, nPorts = rtmidi->getPortCount();
  if ( nPorts == 0 ) {
    if ( isInput )
      std::cout << "No input ports available!" << std::endl;
    else
      std::cout << "No output ports available!" << std::endl;
    SLEEP( 1000 ); 
    return false;
  }

  if ( nPorts == 1 ) {
    //std::cout << "\nOpening " << rtmidi->getPortName() << std::endl;
  }
  else {
    for ( i=0; i<nPorts; i++ ) {
      portName = rtmidi->getPortName(i);

      if ( isInput )
        std::cout << "  Input port #" << i << ": " << portName << '\n';
      else
        std::cout << "  Output port #" << i << ": " << portName << '\n';
    }

/*
    do {
      std::cout << "\nChoose a port number: ";
      std::cin >> i;
    } while ( i >= nPorts );
*/
  }

  std::cout << std::endl;
  //rtmidi->openPort( i );
  rtmidi->openPort( 1 );

  return true;
}
