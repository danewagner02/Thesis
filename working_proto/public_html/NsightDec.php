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
$beaconID = 0xFF; /* 1 BYTE*/
$mask = 0xFFFFFFFFFFFFFFFFFFFFFF; /* 10 BYTES*/
$unixtimeBIN=0;
$unixtime = 0;
$milliBIN=0;
$milli = 0;
$numOfFrames = 0;
$tlmFRAMES = array();


$filename = "LCI Message Bus_2017_7_4_16_31_45.tlm";
$handle = fopen("LCI Message Bus_2017_7_4_16_31_45.tlm", "rb");
$i=0;
$loops=0;
///// read in mask
  $contents = fread($handle, 10);
  $mask = bin2hex($contents);// hex mask
  $numOfFrames = substr_count(hexTobinStr($mask), '1'); // get number of frames
while (!feof($handle))
  {


    ///read in unixtime
    $temp = fread($handle, 4);// 4 bytes 32 bits uint
    if($temp!=null){
    $unixtimeBIN = hexTobinStrLSBF(bin2hex($temp));
    $unixtime = bin2UintMSB($unixtimeBIN);

    ///read in millisec
    $temp = fread($handle, 2);// 2 bytes 16 bits uint

    $milliBIN = hexTobinStrLSBF(bin2hex($temp));
    $milli = bin2UintMSB($milliBIN);
    //echo ("--4 bytes  LSBF(unix time)---->".$unixtime);
    //echo("------<>-------");
    //echo ("--2 bytes  LSBF(millisec)---->".$milli);
    ///read in tlm frames
    for ($j=0; $j < $numOfFrames; $j++)
    {
      //echo ("{--$j--}");
      $temp = fread($handle, 6);// 6 bytes 32 bits uint
      $tlmFRAMES[$j] = hexTobinStrLSBF(bin2hex($temp));
      if ($j == 2)//current
      {
        $printArr = EpsCurrentOut($tlmFRAMES[$j] );
        echo("------<>-------");
        echo ("frame{".$j."}---->".$printArr[0]."mA ,".$printArr[1]."mA ," .$printArr[2]."mA");
      }
      else if ($j == 3)//temp and boot
      {
        $printArr = EpsTempBoost($tlmFRAMES[$j]);
        echo("------<>------");
        echo ("frame{".$j."}----> Temp1:".$printArr[0]."C ,"."Temp2:".$printArr[1]."C ,"."Temp3:".$printArr[2]."C ,"."BatTemp:".$printArr[3]."C ,"."Battery Mode: ".$printArr[5]);
      }
      else
      {//temp and boot
      /*  echo("------<>-------");
        echo ("frame{".$j."}---->".$tlmFRAMES[$j]);*/
      }
    }



    $loops++;

  }
}
  $contents = fread($handle, 10);
  echo("------<>------- loops--->");
  echo($loops);
fclose($handle);

  /*echo "mask hex--->".$mask;
  echo("------<>-------");
  echo "mask bin-->".hexTobinStr($mask);
  */echo("------<>-------");

  echo $numOfFrames;


function hexTobinStr($hexa)
{
    $string='';
    $bitLen = 4;
    for ($i=0; $i < strlen($hexa); $i+=1)
    {

        $string .= str_pad(decbin(hexdec($hexa[$i])), $bitLen, "0", STR_PAD_LEFT);
    }
    return $string;
}
function hexTobinStrLSBF($hexa)
{
    $string='';
    $bitLen = 4;
    for ($i=strlen($hexa)-1; $i >=0; $i-=2)
    {
        $string .= str_pad(decbin(hexdec($hexa[$i-1])), $bitLen, "0", STR_PAD_LEFT);
        $string .= str_pad(decbin(hexdec($hexa[$i])), $bitLen, "0", STR_PAD_LEFT);
    }
    return $string;
}


function bin2intMSB($string)
{
    $int = 0;
    if($string[0]=="1")
    {
      $int-=pow(2,(strlen($string)-1));
    }

    for ($i=1; $i <(strlen($string)); $i++)
    {
        if($string[$i]=="1")
        {
          $int+=pow(2,(strlen($string)-1)-$i);
        }

    }
    return $int;
}

function bin2UintMSB($string)
{
    $uint = 0;
    for ($i=strlen($string)-1; $i >=0; $i-=1)
    {
        if($string[$i]=="1"){$uint+=pow(2,(strlen($string)-1)-$i);}

    }
    return $uint;
}

function EpsCurrentOut($stringIN)
{

  $current1Str ="";
  $current2Str ="";
  $current3Str ="";
  $current1=0;
  $current2 =0;
  $current3 =0;

  for ($i=0; $i <16; $i++)
  {
      $current1Str.=$stringIN[$i];


  }

  for ($i=16; $i <32; $i++)
  {
      $current2Str.=$stringIN[$i];

  }

  for ($i=32; $i <48; $i++)
  {
      $current3Str.=$stringIN[$i];

  }


  $current1=bin2UintMSB($current1Str);
  $current2=bin2UintMSB($current2Str);
  $current3=bin2UintMSB($current3Str);

  return array($current1,$current2,$current3);

}

function EpsTempBoost($stringIN)
{
  //echo("{---".$stringIN."---}");
  $temp1Str ="";
  $temp2Str ="";
  $temp3Str ="";
  $temp4Str ="";
  $temp5Str ="";
  $batModStr ="";
  $temp1=0;
  $temp2 =0;
  $temp3 =0;
  $temp4 =0;
  $temp5 =0;
  $batMod =0;

  for ($i=0; $i <8; $i++)//Battery mode
  {
      $batModStr.=$stringIN[$i];
  }
  //echo("{".$temp6Str."}");

  for ($i=8; $i <16; $i++)//Cause of last EPS reset
  {
      $temp5Str.=$stringIN[$i];
  }
//echo("{".$temp5Str."}");
  for ($i=16; $i <24; $i++)// temp Battery
  {
      $temp4Str.=$stringIN[$i];
  }
  for ($i=24; $i <32; $i++)//temp 3
  {
      $temp3Str.=$stringIN[$i];
  }
  for ($i=32; $i <40; $i++)//temp 2
  {
      $temp2Str.=$stringIN[$i];
  }
  for ($i=40; $i <48; $i++)//temp 1
  {
      $temp1Str.=$stringIN[$i];
  }
  //echo("<---{".$temp1Str."} , "."{".$temp2Str."} , "."{".$temp3Str."} , "."{".$temp4Str."} , "."{".$temp5Str."} , "."{".$temp6Str."}--->");

  $temp1=bin2IntMSB($temp1Str);
  $temp2=bin2IntMSB($temp2Str);
  $temp3=bin2IntMSB($temp3Str);
  $temp4=bin2IntMSB($temp4Str);
  $temp5=bin2IntMSB($temp5Str);
  $batMod=bin2IntMSB($batModStr);

  return array($temp1,$temp2,$temp3,$temp4,$temp5,$batMod);

}

/* frames
1. nsight state
2. nSIGHT State2
3. EPS Currents Out 1
4. EPS Temperatures and Boot Cause
5. EPS Battery Voltage and Current
6. Command Schedule Information
7. FIPEX Telemetry
8. Current ADCS State
9. Estimated Attitude Angles
10. Estimated Angular Rates
11. Satellite Position (LLH)
12. Fine Sun Vector
13. Rate Sensor Rates
14. Wheel Speed
15. Raw Nadir Sensor
16. Raw Sun Sensor
17. Raw Magnetometer

*/

?>
