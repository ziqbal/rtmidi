<?php

require_once( 'websockets.php' ) ;

class echoServer extends WebSocketServer {

	protected $users = array( ) ;
	protected $total = 0 ;

	public function transmit( $json ) {

		foreach( $this->users as $user ) {

			//print_r($user);
			//print_r($json);

			$this->send( $user , trim( $json ) ) ;

		}

	}

	protected function process( $user , $message ) {


		$t = time( ) ;

		$total = count( $this->users ) ;

		$this->send( $user , "$t OK $total" ) ;

	}

	protected function connected( $user ) {


		$uid = $user->id  ;
		$this->users[ $uid ] = $user ;

		// print_r( $user ) ;

	}

	protected function closed( $user ) {

		$uid = $user->id  ;
		unset( $users[ $uid ] ) ;

	}

}

////

$datafile = '../tests/data.txt' ;

system( "echo FLUSH > $datafile ") ;

$fh = fopen( $datafile , 'r');
$fp = filesize( $datafile ) - 1 ;
fseek( $fh , $fp ) ;

// read some data
//$data = fgets($fp, 4096);

function cb( ) {

	global $fh , $fp ;
	global $echo ;
	global $datafile ;

	clearstatcache( ) ;

	$fs = filesize( $datafile ) ;

	//print_r("$fp $fs\n");

	if( $fp < $fs ) {


		$flagHasData = false ;

		$data = array( ) ;

		while( $fp < $fs ) {

			$line = fgets( $fh , 1024 ) ;

			$len = strlen( $line ) ;

			$fp += $len ;

			if( substr( $line , 0 , 1 ) == 'x' ) {

				$a = explode( ',' , $line ) ;
				$data[] = array( $a[ 2 ] , $a[ 3 ] , $a[ 4 ] ) ;
				//print("CALLBACK ( $fp , $fs ) $line\n");

				$flagHasData = true ;

			}

		}

		if( $flagHasData ) {

			$res = array( ) ;
			$res[ 'midi' ] = array( $data ) ;

			$echo->transmit( json_encode( $res ) ) ;

		}

	} else {

		//print("NO DATA\n");

	}

}

/////

$echo = new echoServer( "0.0.0.0" , "9001" ) ;

try {

  $echo->run( 'cb' ) ;

} catch( Exception $e ) {

  $echo->stdout( $e->getMessage( ) ) ;

}





