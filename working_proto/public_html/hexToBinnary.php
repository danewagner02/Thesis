<?php
/**original AX.25 Frame*/
//$flag = 0x7E /* 1 BYTE*/
//$address = 0xFFFFFFFFFFFFFFFFFFFFFFFFFFFF/* 14 BYTES*/
//$control = 0xFF /*1 BYTE*/
//$pid = 0xFF /*1 BYTE*/
//$info = 0xFF /*N BYTES*/
//$fcs = 0xFFFF /*2 BYTE*/
//$flag2 = 0x7E /* 1 BYTE*/

/*INFO Frame*/
//$beaconID = 0xFF /* 1 BYTE*/
//$mask = 0xFFFFFFFFFFFFFFFFFFFFFF /* 10 BYTES*/
//$numOfFrames = 0
//$tlmFRAMES = array();

/*$handle = @fopen("LCI Message Bus_2017_7_4_16_31_45.tlm", "r");
if ($handle) {
    while (!feof($handle)) {
        $hex = bin2hex($handle);
    }
    fclose($handle);

}

print_r($hex);*/
$filename = "LCI Message Bus_2017_7_4_16_31_45.tlm";
$handle = fopen("LCI Message Bus_2017_7_4_16_31_45.tlm", "rb");
$i=0;
while (!feof($handle))
  {
    $contents = fread($handle, 10);
    $hex = bin2hex($contents);
    echo ($i."-->".$hex);
    echo ("\r\n");
    $i+=1;

  /*  $hex = unpack("H*", fgets($handle));
    echo (current($hex));
    echo ("****".$i."****");*/

  }
fclose($handle);
/*$handle = @fopen("LCI Message Bus_2017_7_4_16_31_45.tlm", "r");
  if ($handle) {
      while (!feof($handle))
      {
        echo (fgets($handle));
      }
      fclose($handle);

  }*/
/*$filename = "LCI Message Bus_2017_7_4_16_31_45.tlm";
$handle = fopen($filename, "rb");
if ($handle) {
    while (!feof($handle))
    {
      echo (fgets($handle));
    }
    fclose($handle);
}*/

?>
