

<script>

var audioCtx = new AudioContext();


// create the waveshaper
var waveShaper = audioCtx.createWaveShaper();
 
// our distortion curve function
function makeDistortionCurve(amount) {
    var k = typeof amount === 'number' ? amount : 50,
        n_samples = 44100,
        curve = new Float32Array(n_samples),
        deg = Math.PI / 180,
        i = 0,
        x;
    for ( ; i < n_samples; ++i ) {
        x = i * 2 / n_samples - 1;
        curve[i] = ( 3 + k ) * x * 20 * deg / 
            (Math.PI + k * Math.abs(x));
    }
    return curve;
}
 
// connect the nodes
sourceNode.connect(convolver);
convolver.connectwaveShaper();
waveShaper.connect(audioCtx.destination);
 
// vary the amount of distortion with the slider
distortionSlider.onMove = function(val){
    waveShaper.curve = makeDistortionCurve(val);
};



distortion.curve = makeDistortionCurve(400);
distortion.oversample = '4x';


oscillator = audioCtx.createOscillator();
var gainNode = audioCtx.createGain();

oscillator.connect(gainNode);
gainNode.connect(audioCtx.destination);

oscillator.type = 'sine'; // sine wave — other values are 'square', 'sawtooth', 'triangle' and 'custom'
oscillator.frequency.value = 2500; // value in hertz
oscillator.start();

</script>