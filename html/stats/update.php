      <?php        
        date_default_timezone_set('America/Toronto');
        if (!isset($_REQUEST['statType']))  { $_REQUEST['statType'] = 'Temperature';}
        if (!isset($_REQUEST['node']))      { $_REQUEST['node'] = '0'; }
        if (!isset($_REQUEST['weekno']))    { $_REQUEST['weekno'] = date("W"); }
        if (!isset($_REQUEST['yearno']))    { $_REQUEST['yearno'] = date("Y"); }

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

        $statType  = $_REQUEST['statType'];
        $timestamp = time() - 2;
        $timestamp = ($timestamp - 14400) * 1000;

        $node = $_REQUEST['node'];
        $yearNo = $_REQUEST['yearno'];
        $weekNo = $_REQUEST['weekno'];

        $query  = "SELECT * FROM sensorDump".$yearNo.$weekNo." WHERE type='".$statType."' AND timestamp >= ".$timestamp;
        $result = $conn->query($query);
        $val = [];
        $timestamp = [];
  
        if($result) {
          while ($row = $result->fetch_assoc()) {
            $val[]       = $row["value"];
            $timestamp[] = $row["timestamp"];
          }
        }

        $result->free();
        $conn->close();

        $i = 0;
        foreach ($val as $value) { 
          $dataOut = array($timestamp[$i], $value);
          echo json_encode($dataOut);
          $i = $i+1;
        }
      ?>
