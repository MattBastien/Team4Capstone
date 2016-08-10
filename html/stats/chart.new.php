<html>
  <head>
    <title>Sensor Statistics</title>
    <link rel="shortcut icon" href="../favicon.png" >
    <script src="jquery.min.js"></script>
    <script src="highcharts.js"></script>
    <script src="exporting.js"></script>
    <script>
      function requestData() {
        $.ajax({
          url: '/stats/update.php',
          success: function(point) {
            var series = chart.series[0],
            shift = series.data.length > 500; // shift if the series is longer than 20
            // add the point
            chart.series[0].addPoint(point, true, shift);
            chart.yAxis[0].isDirty = true;
            chart.redraw();
            
            // call it again after one second
            setTimeout(requestData, 2000);    
          },
          cache: false
        });
      } 
    </script>
    <script>
      <?php
        require("../statusDB.php");
        date_default_timezone_set('America/Toronto');
        if (!isset($_REQUEST['statType']))  { $_REQUEST['statType'] = 'Temperature'; $statUnit = 'Degrees Celsius';}
        if (!isset($_REQUEST['node']))      { $_REQUEST['node'] = '0'; }
        if (!isset($_REQUEST['weekno']))    { $_REQUEST['weekno'] = date("W"); }
        if (!isset($_REQUEST['yearno']))    { $_REQUEST['yearno'] = date("Y"); }

        $statType = $_REQUEST['statType'];
        $statType = ucfirst($statType);
        if ($statType == 'Temperature') { $statUnit = 'Degrees Celsius'; }
        if ($statType == 'Optical')     { $statUnit = 'Lumens'; }

        $node = $_REQUEST['node'];
        $yearNo = $_REQUEST['yearno'];
        $weekNo = $_REQUEST['weekno'];
        $chartTitle = "$statType Statistics";

        $query  = "SELECT * FROM sensorDump".$yearNo.$weekNo." where type='".$statType."'";
        $result = $conn->query($query);

        if (!$result) {
           echo "window.alert('No Data Found for ".$yearNo."-".$weekNo."');</script><script>";
        }
        
        $val = [];
        $timestamp = [];
        while ($row = $result->fetch_assoc()) {
          $val[]       = $row["value"];
          $timestamp[] = $row["timestamp"];
        }

        $result->free();
        $conn->close();

        $dataOut = "var chart; 
                \$(document).ready(function() {
                chart = new Highcharts.Chart({
                ";
        $dataOut .= "chart: {
                  zoomType: 'x',
                  renderTo: 'container',
                  events: {
                    load: requestData
                  }
                  ";
        $dataOut .= "},
              title: {
                  text: '".$chartTitle."'
              },
              xAxis: {
                  type: 'datetime',
                  labels: {
                      rotation: 90,
                  },
                  dateTimeLabelFormats: {
                        millisecond: '%H:%M:%S.%L',
                        second: '%H:%M:%S',
                        minute: '%H:%M',
                        hour: '%l:%M %P',
                        day: '%a %b %e',
                        week: '%e. %b',
                        month: '%b %y',
                        year: '%Y'
                  },
                  tickInterval: 24 * 3600 * 1000,
                  tickWidth: 0,
                  gridLineWidth: 1,
              },
              yAxis: {
                  title: {
                      text: '".$statUnit."'
                  },
                  min: 0,
                  isDirty: true
              },
              legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
              },
              plotOptions: {
                  series: {
                      marker: {
                          enabled: false
                      },
                      states: {
                          hover: {
                              enabled: true,
                              lineWidth: 5
                          }
                      }
                  },
                  areaspline: {
                      stacking: 'normal',
                  }
              },
              tooltip: {
                  headerFormat: '<b>{series.name}</b><br>',
                  pointFormat: '{point.x:%a, %b %e %Y @ %l:%M %P}<br> {point.y} ".$statUnit."'
              },";
        $dataOut .= "series: [{";
        //Insert data points
        $dataOut .= "name: 'sensorTag',data: [";
        $i = 0;
        foreach ($val as $value) { 
          $dataOut .= "[".$timestamp[$i].",".$value."]";
          if (isset($timestamp[$i+1])) {
            $dataOut .= ",";
          } else {
            $dataOut .= "]";
          }
          $i = $i+1;
        }
        $dataOut .= "}]\n";
        $dataOut .= "        });
    });";
        echo $dataOut;
      ?>

    </script>
  </head>

  <body>
    <?php require("/usr/share/nginx/html/stats/menu.php")?>
    <div id="container" style="min-width: 310px; height: 98%; margin: 0 auto"></div>
  </body>
</html>
