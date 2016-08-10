<?php
  date_default_timezone_set('America/Toronto'); 
  $nodeValue = @$_REQUEST['node'];
  $statTypeValue = @$_REQUEST['statType'];
  $statOptionValue = @$_REQUEST['statOption'];
  $weeknoValue = @$_REQUEST['weekno'];  if (!isset($weeknoValue)) $weeknoValue = date("W");
  $yearnoValue = @$_REQUEST['yearno'];  if (!isset($yearnoValue)) $yearnoValue = date("Y");
?>
<form action='chart.php'>
Node <select id=node name='node'>
  <option value='0' <?php if ($nodeValue == '0') { echo 'selected="selected"'; }?>>SensorTag</option>
</select>
&nbsp|&nbsp
Stat Type <select id=statType name='statType'>
  <option value='Temperature' <?php if ($statTypeValue == 'Temperature') { echo 'selected="selected"'; }?>>Temperature</option>
  <option value='Optical' <?php if ($statTypeValue == 'Optical') { echo 'selected="selected"'; }?>>Optical</option>
</select>
&nbsp|&nbsp
Week No <input type='textbox' size=2 id=weekno name=weekno value='<?php echo $weeknoValue;?>'/>
&nbsp|&nbsp
Year <input type='textbox' size=4 id=yearno name=yearno value='<?php echo $yearnoValue;?>'/>
&nbsp|&nbsp
<input type='submit'>
</form>
