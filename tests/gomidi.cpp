//*****************************************//
//  sysextest.cpp
//  by Gary Scavone, 2003-2005.
//
//  Simple program to test MIDI sysex sending and receiving.
//
//*****************************************//

#include <iostream>
#include <cstdlib>
#include <typeinfo>
#include "RtMidi.h"

// Platform-dependent sleep routines.
#if defined(__WINDOWS_MM__)
  #include <windows.h>
  #define SLEEP( milliseconds ) Sleep( (DWORD) milliseconds ) 
#else // Unix variants
  #include <unistd.h>
  #define SLEEP( milliseconds ) usleep( (unsigned long) (milliseconds * 1000.0) )
#endif

// This function should be embedded in a try/catch block in case of
// an exception.  It offers the user a choice of MIDI ports to open.
// It returns false if there are no ports available.
bool chooseMidiPort( RtMidi *rtmidi );

long long frame = 0 ;


void mycallback( double deltatime, std::vector< unsigned char > *message, void * /*userData*/ )
{
  unsigned int nBytes = message->size();
  std::cout << "x," << frame << "," ;
  for ( unsigned int i=0; i<nBytes; i++ )
    std::cout << (int)message->at(i) << ",";


  if ( nBytes > 0 )
    std::cout << deltatime << ",y" << std::endl;

  frame++;
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

  SLEEP( 60000 ); 

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

    do {
      std::cout << "\nChoose a port number: ";
      std::cin >> i;
    } while ( i >= nPorts );
  }

  std::cout << std::endl;
  rtmidi->openPort( i );

  return true;
}
