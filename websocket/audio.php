<html><head><title>WebSocket</title>
<style type="text/css">
html,body {
	font:normal 0.9em arial,helvetica;
}
#log {
	width:600px; 
	height:300px; 
	border:1px solid #7F9DB9; 
	overflow:auto;
}
#msg {
	width:400px;
}
</style>
<script type="text/javascript">
var socket;

function init() {
	var host = "ws://127.0.0.1:9000/echobot"; // SET THIS TO YOUR SERVER
	try {
		socket = new WebSocket(host);
		log('WebSocket - status '+socket.readyState);
		socket.onopen    = function(msg) { 
							   log("Welcome - status "+this.readyState); 
							   tick();
						   };
		socket.onmessage = function(msg) { 
							   //log("Received: "+msg.data); 
							   //console.log(msg.data);
							   if(msg.data.substring(0,1)=='['){
							   //console.log(msg.data);
							   _controller(JSON.parse(msg.data));
								}


						   };
		socket.onclose   = function(msg) { 
							   log("Disconnected - status "+this.readyState); 
						   };
	}
	catch(ex){ 
		log(ex); 
	}
	$("msg").focus();
}

function send(){
	var txt,msg;
	txt = $("msg");
	msg = txt.value;
	if(!msg) { 
		alert("Message can not be empty"); 
		return; 
	}
	txt.value="";
	txt.focus();
	try { 
		socket.send(msg); 
		log('Sent: '+msg); 
	} catch(ex) { 
		log(ex); 
	}
}
function quit(){
	if (socket != null) {
		log("Goodbye!");
		socket.close();
		socket=null;
	}
}

function reconnect() {
	quit();
	init();
}

function tick(){
	socket.send("X"); 
	setTimeout(tick,1);
};

// Utilities
function $(id){ return document.getElementById(id); }
function log(msg){ $("log").innerHTML+="<br>"+msg; 

  var elem = document.getElementById('log');
  elem.scrollTop = elem.scrollHeight;

}
function onkey(event){ if(event.keyCode==13){ send(); } }


function _controller(data){

	// 176, 49 , 1/127

	data.forEach(function(entry){
		//console.log(entry);
		if(entry[0]==176 && entry[1]==49){
			if(entry[2]==1){
				//console.log('RIGHT CLOCKWISE');
				oscillator.frequency.value+=1;
			}
			if(entry[2]==127){
				oscillator.frequency.value+=-1;
			 //console.log('RIGHT ANTI CLOCKWISE');
			}
		}

		if(entry[0]==176 && entry[1]==51){
			// PRESSED 
			if(entry[2]==1){
				//console.log('RIGHT CLOCKWISE');
				oscillator.frequency.value+=100;
			}
			if(entry[2]==127){
				oscillator.frequency.value+=-100;
			 //console.log('RIGHT ANTI CLOCKWISE');
			}
		}		

		if(entry[0]==176 && entry[1]==48){
			if(entry[2]==1) console.log('LEFT CLOCKWISE');
			if(entry[2]==127) console.log('LEFT ANTI CLOCKWISE');
		}
		if(entry[0]==176 && entry[1]==50){
			if(entry[2]==1) console.log('PRESSED LEFT CLOCKWISE');
			if(entry[2]==127) console.log('PRESSED LEFT ANTI CLOCKWISE');
		}		

	});


}
</script>

<?php include("audio-core.php"); ?>

</head>
<body onload="init()">
<h3>WebSocket v2.00</h3>
<div id="log"></div>
<input id="msg" type="textbox" onkeypress="onkey(event)"/>
<button onclick="send()">Send</button>
<button onclick="quit()">Quit</button>
<button onclick="reconnect()">Reconnect</button>
</body>
</html>