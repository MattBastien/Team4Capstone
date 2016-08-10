<?php
  require 'statusDB.php';
  initToggle();
?>

<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <title>Smart Lab Statistics</title>
  <!---Mobile screen compensation-->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="css/vendor.min.css">
  <link rel="stylesheet" href="css/main.min.css">
  <link rel="stylesheet" href="css/base.min.css">
  <link type="text/css" rel="stylesheet" href="toggler/css/themes/toggles-light.css">
  <link type="text/css" rel="stylesheet" href="toggler/css/toggles.css">
  <script src="js/jquery-1.11.3.min.js"></script>
  <script type="text/javascript" src="toggler/toggles.min.js"></script>
  <link rel="shortcut icon" href="favicon.png" >
</head>

<body>
  <header id="main-header">
    <div class="row">
      <nav id="nav-wrap">
        <a class="mobile-btn" href="#nav-wrap" title="Show navigation">
          <span class="menu-icon">Menu</span>
        </a>
        <a class="mobile-btn" href="#" title="Hide navigation">
          <span class="menu-icon">Menu</span>
        </a>
	<ul align="center" id="nav" class="nav">
	  <li><a class="smoothscroll" href="#home">Home.</a></li>
          <li class="current"><a class="" href="stats/chart.php">Sensor Metrics.</a></li>
          <li><a class="smoothscroll" href="#automation">Automation.</a></li>
          <li><a class="smoothscroll" href="#security">Security.</a></li>
          <li><a class="smoothscroll" href="#twitter">Twitter.</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section id="home">
    <div class="row home-content">
      <div class="twelve columns home-container">
        <div id="home-slider" class="flexslider">
          <ul class="slides">
            <li>
              <div class="flex-caption">
                <h1 class="">IoT Smart Lab.</h1>
	        <h3 class="">University of Windsor<br>Capstone Design Project<br>Team 4 - Darius Saif, Matthew Bastien</h3>
	      </div>
	    </li>
            <li>
              <div class="flex-caption">
    	        <h1 class="">Wireless Sensor Network.</h1>
    	        <h3 class="">View Real-time sensor statistics!</h3>
              </div>
            </li>
            <li>
              <div class="flex-caption">
	        <h1 class="">Interact with <a class="smoothscroll" href="#automation" title="automation" >devices</a></h1>
	        <h3 class="">Power on/off multiple devices, monitor video feed, and engage the door</h3>
	      </div>
           </li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section id="metrics">
    <div class="row section-head">
    </div>
  </section>

  <section id="automation">
    <div style="position: relative;" class="row content flex-container">
      <table style="position: absolute; left: 12%; width: 90%">
        <tr>
          <td style="width: 33%;">
            <h4 class"">Device0.</h4><br>
            <div style="width: 20%;" id="device0" value="<?php echo $lightVal;?>" class="lightsw toggle-light"></div>
            <script type="text/javascript">$('.lightsw').toggles();</script>
          </td>

          <td style="width: 33%;">
            <h4 class"">Door.</h4><br>
            <div style="width: 40%;" id="device1" value="<?php echo $dev1Val;?>" class="dev1 toggle-light"></div>
            <script type="text/javascript">$('.dev1').toggles();</script>
          </td>

          <td style="width: 33%;">
            <h4 class"">Outlet.</h4><br>
            <div style="width: 60%;" id="device2" value="<?php echo $dev2Val;?>" class="dev2 toggle-light"></div>
            <script type="text/javascript">$('.dev2').toggles();</script>
          </td>
        </tr>
      </table>

      <div style="z-index: 1; left: 52%;" class="row content flex-container" id="go-top">
        <a style="position: absolute; top: 100px;" class="smoothscroll" title="Submit" href="javascript:void(0)" onclick="submitFcn()" >Submit<i class="fa fa-angle-up"></i></a>
      </div>
      <div style="position:relative; top: 150px; left: 43%;">
        <h4>Current is <?php if ($currentVal == 0) {echo " not";}?> flowing</h4>
        <h4>The door is <?php if ($doorStateVal == 1) {echo " CLOSED";}else{echo " OPEN";}?></h4>
      </div>
    </div>
  </section>

  <section id="security" style="min-height: 250px">
    <div style="position: relative; top: 150px;" class="row content flex-container">
      <table style="position: absolute; left: 12%; width: 90%">
        <tr>
          <td style="width: 33%;">
            <h4 class"">Video Feed.</h4><br>
            <div style="position: absolute; width: 20%;" id="videopane" value="0" class="video toggle-light" onclick="togglePane('videofeed')"></div><br>
            <script type="text/javascript">$('.video').toggles();</script>
          </td>
        </tr>
      </table>
    </div>
  </section>

  <section id="twitter">
    <div class="row content flex-container">
      <a class="twitter-timeline" href="https://twitter.com/IoTSmartLab" data-widget-id="689614235479973889">Tweets by @IoTSmartLab</a>
      <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    </div>
  </section>

  <footer>
    <div class="row">
      <div id="go-top">
        <a class="smoothscroll" title="Back to Top" href="#home">Back to Top<i class="fa fa-angle-up"></i></a>
      </div>
      <h4>Inspired by Styleshout's Kreo10.</h4>
    </div>
  </footer>

  <script src="js/misc.js"></script>
  <script src="js/jquery.flexslider-min.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.fittext.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/main.js"></script>
</body>
</html>
