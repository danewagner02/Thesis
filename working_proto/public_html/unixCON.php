<?php
  $timestamp = time();
  $year = date('Y',$timestamp);
  $month = date('m',$timestamp);
  $day = date('d',$timestamp);
  $hour = date('h',$timestamp);
  $minute = date('i',$timestamp);
  $second = date('s',$timestamp);

  echo ($timestamp."--".$year."--".$month."--".$day."--".$hour."--".$minute."--".$second);



?>
