<script type="text/javascript" src="lms/scripts/jquery.plugin.min.js"></script>
<script type="text/javascript" src="lms/scripts/jquery.countdown.min.js"></script>
<script type="text/javascript" src="lms/scripts/jquery.countdown-ru.js"></script>
<link rel="stylesheet" href="lms/css/font-awesome/css/font-awesome.min.css">
<style type="text/css">
.ui-widget { font-family: Verdana,Arial,sans-serif; font-size: 0.9em;}
.button_disabled { background: #D1D4D8;  }
.button_enabled {  }
.red_bg { 	color: red;
    font-weight: bold;
 }
#spinner
{
  display: none;
  position: fixed;
	top: 50%;
	left: 50%;
	margin-top: -22px;
	margin-left: -22px;
	background-position: 0 -108px;
	opacity: 0.8;
	cursor: pointer;
	z-index: 8060;
  width: 44px;
	height: 44px;
	background: #000 url('lms/scripts/fancybox_loading.gif') center center no-repeat;
  border-radius:7px;
}
.is-countdown {
}
.countdown-rtl {
	direction: rtl;
}
.countdown-holding span {
	color: #888;
}
.countdown-row {
	clear: both;
	width: 100%;
	padding: 0px 2px;
	text-align: center;
}
.countdown-show1 .countdown-section {
	width: 98%;
}
.countdown-show2 .countdown-section {
	width: 48%;
}
.countdown-show3 .countdown-section {
	width: 32.5%;
}
.countdown-show4 .countdown-section {
	width: 24.5%;
}
.countdown-show5 .countdown-section {
	width: 19.5%;
}
.countdown-show6 .countdown-section {
	width: 16.25%;
}
.countdown-show7 .countdown-section {
	width: 14%;
}
.countdown-section {
	display: block;
	float: left;
	font-size: 75%;
	text-align: center;
}
.countdown-amount {
    font-size: 200%;
}
.countdown-period {
    display: block;
}
.countdown-descr {
	display: block;
	width: 100%;
}
#defaultCountdown { 
 display:block;
 font-family:Arial;
 text-align: center; 
 width: 240px; 
 height: 40px; 
 font-size: 0.7em;
 left: 50%;
 position: absolute;
 margin-left: -120px;
}
#buttonset { 
display:block;  font-family: 'Helvetica', 'Arial';  text-align: center;   width: 700px;   height: 40px;   left: 50%;  bottom : 0px;  position: absolute;  margin-left: -350px; } 
#buttonsetm { 
display:block;  font-family: 'Helvetica', 'Arial';  width: 100%; top: 0px; bottom : 50px;  position: absolute; overflow: auto;} 
.timer-top {
  text-align: center;
  width: 100%;
  margin: auto;
  left: 0; bottom: 0; right: 0;
  top:0px;
 }
.timer-top-but {
 display:block;
 bottom:0;
 margin:0 0 0 100%;
 padding:6px 12px 4px;
 color:white;
 font-family:Arial;
 font-size: 0.5em;
 text-decoration: none;
 }
.ui-progressbar {
    position: relative;
 }
.progress-label {
    position: absolute;
    left: 45%;
    top: 4px;
    font-weight: bold;
    text-shadow: 1px 1px 0 #fff;
 } 
.sequence ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
.sequence li { margin: 10px; padding: 10px; width: 95%; border: 2px solid #969090; }
.accord ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
.accord li { margin: 10px; padding: 10px; width: 95%; border: 2px solid #969090; }
.icon-invisible {
    visibility: hidden;
}
</style>
