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
$numOfFrames = 0;
$tlmFRAMES = array();

//Flags
$EPS_Temperatures_and_Boot_Cause_F = False; //-->  bit 15 F_array[4]
$EPS_Battery_Voltage_and_Current_F = False; //-->  bit 14 F_array[3]
$nSIGHT_State_F = False;                   //-->  bit 4 F_array[2]
$nSIGHT_State2_F = False;                  //-->  bit 3 F_array[1]
$EPS_Currents_Out_1_F = False;             //-->  bit 2 F_array[0]
$loopCompleteFlag = False;

//Frame Counters
$EPS_Temperatures_and_Boot_Cause_Frame_Num = 4; //-->  bit 15; F_array[4];Fnum 4
$EPS_Battery_Voltage_and_Current_Frame_Num = 5; //-->  bit 14 ;F_array[3];Fnum 5
$nSIGHT_State_Frame_Num = 1;                   //-->  bit 4; F_array[2];Fnum 1
$nSIGHT_State2_Frame_Num = 2;                  //-->  bit 3; F_array[1];Fnum 2
$EPS_Currents_Out_1_Frame_Num = 3;             //-->  bit 2; F_array[0];Fnum 3


$filename = "LCI Message Bus_2017_7_4_16_31_45.tlm";
$handle = fopen("LCI Message Bus_2017_7_4_16_31_45.tlm", "rb");
$i=0;
$loops=0;
///// read in mask
  $contents = fread($handle, 10);
  $mask = bin2hex($contents);// hex mask
  $numOfFrames = substr_count(hexTobinStr($mask), '1'); // get number of frames
  //echo (hexTobinStr($mask));

  //setTelemetryFlags
  $FlagArray = maskFlagSetter(hexTobinStr($mask));

  //defult Record entry templete
  $recordEntry = array('sat1', 'logTime->1', 'Date->2', 'Time->3', 'sec->4', 'logMilli->5', 'BatVoltage->6',
   'currentIN->7', 'currentOut->8', 'toADCS5V->9', 'toADCS3V3->10', 'toGPS->11', 'BatTemp->12',
    'Convert1temp->13', 'Convert2temp->14', 'Convert3temp->15');

$oneEntFlag = 0;
while (!feof($handle))
  {


    ///read in unixtime
    $temp = fread($handle, 4);// 4 bytes 32 bits uint
    if($temp!=null)
    {
        $unixtimeBIN = hexTobinStrLSBF(bin2hex($temp));
        $unixtime = bin2UintMSB($unixtimeBIN);
        $recordEntry[1] = $unixtime;

        ///read in millisec
        $temp = fread($handle, 2);// 2 bytes 16 bits uint
        $milliBIN = hexTobinStrLSBF(bin2hex($temp));
        $milli = bin2UintMSB($milliBIN);
        $recordEntry[5] = $milli;

        ///read in tlm frames
        for ($j=1; $j <= $numOfFrames; $j++)
        {
          $temp = fread($handle, 6);// 6 bytes 32 bits uint
          $tlmFRAMES[$j] = hexTobinStrLSBF(bin2hex($temp));

          if ($j == 1 )//(int)$nSIGHT_State_Frame_Num)//$nSIGHT_State
          {
            if ($nSIGHT_State_F==True)
            {
              //echo("------>".$j."nSIGHT_State<-------");
              //echo("nSIGHT_State");
              //echo("------>nSIGHT_State end<------");
            }
            else
            {

            }
          }

          elseif ($j ==2)//(int) $nSIGHT_State2_Frame_Num)//$nSIGHT_State2_Frame_Num
          {
            if ($nSIGHT_State2_F==True)
            {
              //echo("------>".$j."nSIGHT_State2<------");
              //echo("nSIGHT_State2");
              //echo("------>nSIGHT_State2 end<------");
            }
            else
            {

            }
          }
          elseif ($j == 3)//(int)$EPS_Currents_Out_1_Frame_Num)//$EPS_Currents_Out_1
          {
            if ($EPS_Currents_Out_1_F==True)
            {
              $printArr = EpsCurrentOut($tlmFRAMES[$j]);
              $recordEntry[11] = (int)$printArr[0];
              $recordEntry[10] = (int)$printArr[1];
              $recordEntry[9] = (int)$printArr[2];
            //  echo("------>".$j."EPS_Currents_Out_1<------");
              //echo(json_encode($printArr));
            //  echo("------>EPS_Currents_Out_1 end<------");
            }
            else
            {

            }
          }
          elseif ($j ==4)//(int) $EPS_Temperatures_and_Boot_Cause_Frame_Num)//$EPS_Temperatures_and_Boot_Cause
          {
            if ($EPS_Temperatures_and_Boot_Cause_F==True)
            {
              $printArr = EpsTempBoost($tlmFRAMES[$j]);
              $recordEntry[13] = (int)$printArr[0];
              $recordEntry[14] = (int)$printArr[1];
              $recordEntry[15] = (int)$printArr[2];
              $recordEntry[12] = (int)$printArr[3];
              //echo("------>".$j."EPS_Temperatures_and_Boot_Cause<------");
              //echo(json_encode($printArr));
              //echo("------>EPS_Temperatures_and_Boot_Cause end<------");
            }
            else
            {

            }
          }
          elseif ($j == 5)//(int)$EPS_Battery_Voltage_and_Current_Frame_Num)//$EPS_Battery_Voltage_and_Current
          {
            if ($EPS_Battery_Voltage_and_Current_F==True)
            {
              $printArr = EPS_Battery_Voltage_and_Current($tlmFRAMES[$j]);
              $recordEntry[8] = (int)$printArr[0];
              $recordEntry[7] = (int)$printArr[1];
              $recordEntry[6] = (int)$printArr[2];
              //echo("------>".$j."EPS_Battery_Voltage_and_Current<------");
              //echo(json_encode($printArr));
              //echo("------>EPS_Battery_Voltage_and_Current end<------");
            }
            else
            {

            }
          }
          else
          {
            //echo "skip";
          }

        }
        $datedata = unixCon($recordEntry[1]);//array($timestamp,$year,$month,$day,$hour,$minute,$second,$date,$time,$fullDate);
        $recordEntry[2]=$datedata[9];
        $recordEntry[3]=$datedata[8];
        $recordEntry[4]=$datedata[6];
      /*  echo ("ID:sat1". "---logTime:".$recordEntry[1]. "---Date:".$recordEntry[2]. "---Time:".
              $recordEntry[3]. "---sec:".$recordEntry[4]."---logMilli:".$recordEntry[5].
               "---BatVoltage:".$recordEntry[6]."---currentIN:".$recordEntry[7]. "---currentOut:".$recordEntry[8].
                "---toADCS5V:".$recordEntry[9]. "---toADCS3V3:".$recordEntry[10]. "---toGPS:".$recordEntry[11].
                "---BatTemp:".$recordEntry[12]."---Convert1temp:".$recordEntry[13]. "---Convert2temp:".$recordEntry[14].
                "---Convert3temp:".$recordEntry[15]."\n\n");*/
        echo ("\n");

          //echo("-------------print entry---------");
//--------->insertRec($recordEntry);



    }
//$loops++;
  }
  $contents = fread($handle, 10);//read till end of file
  fclose($handle);//close file



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

  $current1Str =""; // Current supplied to ADCS 5V.
  $current2Str =""; //Current supplied to ADCS 3V3.
  $current3Str =""; //Current supplied to GPS Receiver 3V3.
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
  $ConTemp1Str ="";
  $ConTemp2Str ="";
  $ConTemp3Str ="";
  $BatTempStr ="";
  $resetStr ="";
  $batModStr =""; // 0->nominal  1->UnderVoltage 2->Overvoltage
  $temp1=0;       //Converter 1 temp
  $temp2 =0;      //Converter 2 temp
  $temp3 =0;      //Converter 3 temp
  $temp4 =0;      //Battery temp
  $resetCause =0;      //resetCause
  $batMod =0;     //BatteryMode

  for ($i=0; $i <8; $i++)//Battery mode
  {
      $batModStr.=$stringIN[$i];
  }
  for ($i=8; $i <16; $i++)//Cause of last EPS reset
  {
      $resetStr.=$stringIN[$i];
  }
  for ($i=16; $i <24; $i++)// temp Battery
  {
      $BatTempStr.=$stringIN[$i];
  }
  for ($i=24; $i <32; $i++)//temp 3
  {
      $ConTemp3Str.=$stringIN[$i];
  }
  for ($i=32; $i <40; $i++)//temp 2
  {
      $ConTemp2Str.=$stringIN[$i];
  }
  for ($i=40; $i <48; $i++)//temp 1
  {
      $ConTemp1Str.=$stringIN[$i];
  }

  $temp1=bin2IntMSB($ConTemp1Str);
  $temp2=bin2IntMSB($ConTemp2Str);
  $temp3=bin2IntMSB($ConTemp3Str);
  $temp4=bin2IntMSB($BatTempStr);
  $resetCause=bin2IntMSB($resetStr);
  $batMod=bin2IntMSB($batModStr);

  return array($temp1,$temp2,$temp3,$temp4,$resetCause,$batMod);

}

function EPS_Battery_Voltage_and_Current($stringIN)
{

  $Batery_Voltage_Str =""; // Battery voltage.
  $Current_from_boost_Converters_Str =""; //Current from boost converters.
  $Current_Out_Of_Battery_Str =""; //Current out of battery
  $Batery_Voltage =0; // Battery voltage.
  $Current_from_boost_Converters =0; //Current from boost converters.
  $Current_Out_Of_Battery =0; //Current out of battery

  for ($i=0; $i <16; $i++)
  {
      $Batery_Voltage_Str.=$stringIN[$i];
  }

  for ($i=16; $i <32; $i++)
  {
      $Current_from_boost_Converters_Str.=$stringIN[$i];
  }

  for ($i=32; $i <48; $i++)
  {
      $Current_Out_Of_Battery_Str.=$stringIN[$i];
  }


  $Batery_Voltage=bin2UintMSB($Batery_Voltage_Str);
  $Current_from_boost_Converters=bin2UintMSB($Current_from_boost_Converters_Str);
  $Current_Out_Of_Battery=bin2UintMSB($Current_Out_Of_Battery_Str);

  return array($Batery_Voltage,$Current_from_boost_Converters,$Current_Out_Of_Battery);

}
function maskFlagSetter($mask_IN)
{
  //$returningArr = array(False,False,False,False,False);

  if(substr($mask_IN,13,1)=='1')
  {
    $GLOBALS['EPS_Battery_Voltage_and_Current_F'] = True;
    $GLOBALS['EPS_Battery_Voltage_and_Current_Frame_Num']= (int)substr_count(substr($mask_IN,0,13), '1');
    //echo("b4 14->".json_encode($GLOBALS['EPS_Battery_Voltage_and_Current_Frame_Num'])." ; ");
  }
  if(substr($mask_IN,14,1)=='1')
  {
    $GLOBALS['EPS_Temperatures_and_Boot_Cause_F'] = True;
    $GLOBALS['EPS_Temperatures_and_Boot_Cause_Frame_Num']= (int)substr_count(substr($mask_IN,0,14), '1');
    //echo("b4 15->".json_encode($GLOBALS['EPS_Temperatures_and_Boot_Cause_Frame_Num'])." ; ");
  }
  if(substr($mask_IN,1,1)=='1')
  {
    $GLOBALS['EPS_Currents_Out_1_F'] = True;
    $GLOBALS['EPS_Currents_Out_1_Frame_Num'] = (int)substr_count(substr($mask_IN,0,1), '1');
    //echo("b4 2->".json_encode($GLOBALS['EPS_Currents_Out_1_Frame_Num'])." ; ");
  }
  if(substr($mask_IN,2,1)=='1')
  {
    $GLOBALS['nSIGHT_State2_F']=True;
    $GLOBALS['nSIGHT_State2_Frame_Num']= (int)substr_count(substr($mask_IN,0,2), '1');
    //echo("b4 3->".json_encode($GLOBALS['EPS_Currents_Out_1_Frame_Num'])." ; ");
  }
  if(substr($mask_IN,3,1)=='1')
  {
    $GLOBALS['nSIGHT_State_F']=True;
    $GLOBALS['nSIGHT_State_Frame_Num']= (int)substr_count(substr($mask_IN,0,3), '1');
    //echo("b4 4->".json_encode($GLOBALS['nSIGHT_State_Frame_Num'])." ; ");
  }
  //return $returningArr;
}
function unixCon($timestamp)
{
  //$timestamp = time();
  $year = date('Y',$timestamp);
  $month = date('m',$timestamp);
  $day = date('d',$timestamp);
  $date = date('Y:m:d',$timestamp);
  $hour = date('h',$timestamp);
  $minute = date('i',$timestamp);
  $time = date('h:i:s',$timestamp);
  $second = date('s',$timestamp);
  $fullDate=date('Y:m:d:h:i:s',$timestamp);

  return  array($timestamp,$year,$month,$day,$hour,$minute,$second,$date,$time,$fullDate);
}
function insertRec($recEntry)
{
  $servername = "localhost";
  $username = "Admin";
  $password = "pass";

  // Set the JSON header
  header("Content-type: text/json");

  try
  {
      $conn2 = new PDO("mysql:host=$servername;dbname=thesisDB", $username, $password);
      // set the PDO error mode to exception
      $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //insert a recordEntry
      $sql = 'INSERT INTO sats (ID, logTime, Da'.'te, Time, sec,
       logMilli, BatVoltage, currentIN, currentOut,
        toADCS5V, toADCS3V3, toGPS, BatTemp, Convert1temp,
         Convert2temp, Convert3temp) VALUES ("'.$recEntry[0].'", "'.$recEntry[1].'", "'.$recEntry[2].'", "'.
         $recEntry[3].'", "'.$recEntry[4].'","'.$recEntry[5].'", "'.$recEntry[6].'", "'.$recEntry[7].
          '", "'.$recEntry[8].'", "'.$recEntry[9].'", "'.$recEntry[10].'","'.$recEntry[11].
           '", "'.$recEntry[12].'","'.$recEntry[13].'", "'.$recEntry[14].'", "'.$recEntry[15].'");';
      $conn2->query($sql);


  }
  catch(PDOException $e)
  {
      echo "Connection failed: " . $e->getMessage();
  }


}

/* frames order
1. nsight state                    -->  bit 4
2. nSIGHT State2                   -->  bit 3
3. EPS Currents Out 1              -->  bit 2
4. EPS Temperatures and Boot Cause -->  bit 15
5. EPS Battery Voltage and Current -->  bit 14
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
