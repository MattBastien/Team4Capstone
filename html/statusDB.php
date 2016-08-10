<?php
  //Poll for client end adjustments
  $lightVal   = @$_REQUEST['lightsw'];
  $dev1Val    = @$_REQUEST['dev1'];
  $dev2Val    = @$_REQUEST['dev2'];
  $currentVal;
  $doorStateVal;  

  $servername = "localhost";
  $username   = "root";
  $password   = "Z3r0Bas3";
  $dbname     = "status";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


  //Read and write from the DB
  function grabBit($devName,$id) {
    if (isset($GLOBALS[$devName])) {
      $command = "UPDATE Toggles SET Value=".$GLOBALS[$devName]." WHERE id='".$id."'";
      $GLOBALS['conn']->query($command);
    } else {
      $query = "SELECT * FROM Toggles WHERE id='".$id."'";
      $result = $GLOBALS['conn']->query($query);
      $row = $result->fetch_assoc();
      $GLOBALS[$devName] = $row['Value'];
    }
  }

  function initToggle() {
    if (isset($GLOBALS['dev1Val'])) { shell_exec("/home/pi/Sensor_Data_Acquisition2/send_req.py -d dev1 -u ".$GLOBALS['dev1Val']); }
    if (isset($GLOBALS['dev2Val'])) { shell_exec("/home/pi/Sensor_Data_Acquisition2/send_req.py -d dev2 -u ".$GLOBALS['dev2Val']); }

    grabBit('lightVal','Lights');
    grabBit('dev1Val','Dev1');
    grabBit('dev2Val','Dev2');
    grabBit('currentVal','Current');
    grabBit('doorStateVal','doorOpen');

    $GLOBALS['conn']->close();
    //shell_exec("/home/pi/toggleLight.sh ".$GLOBALS['lightVal']); //TODO Only for testing
  }
?>
