<?php
$servername = "localhost";
$username = "Admin";
$password = "pass";
$voltagedata = [];
$currentINdata = array();
$currentOutdata = array();
$sID = "sat1";
//$dTails = "Voltage";
$dTails = "current";
//$dTails = "currentOut";
//$dTails = "temperature";

if (isset($_GET['sID'])) {
    $sID  = $_GET['sID'];

}
if (isset($_GET['details'])) {
    $dTails  = $_GET['details'];

}
//setcookie("satIdQ", $sID, time() + (86400), "/"); // 86400 = 1 day
// Set the JSON header
header("Content-type: text/json");

try {
    $conn = new PDO("mysql:host=$servername;dbname=thesisDB", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"."\n";
    if($dTails=="Voltage"){getVoltage($conn,$sID);}
    if($dTails=="current"){getCurr($conn,$sID);}
    if($dTails=="currentOut"){getCurrOut($conn,$sID);}
    if($dTails=="temperature"){getTemp($conn,$sID);}


    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }


function getVoltage($conn,$sID)
{
    $sql = 'SELECT Da'.'te, BatVoltage FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,Battery Voltage\n");
    foreach ($conn->query($sql) as $row)
    {
        //$tempArr = array((int)$row['Date'],floatval($row['voltage']));
        //array_push($GLOBALS['voltagedata'] , $tempArr );
        echo (($row['Date'].",".floatval($row['BatVoltage'])."\n"));

    }
}

function getCurr($conn,$sID)
{
    $sql = 'SELECT Da'.'te, currentIN, currentOut FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,Current From boost converters,Current Out Battery\n");
    foreach ($conn->query($sql) as $row)
    {
        echo (($row['Date'].",".floatval($row['currentIN']).",".floatval($row['currentOut'])."\n"));
    }
}
function getCurrOut($conn,$sID)
{
    $sql = 'SELECT Da'.'te, toADCS5V, toADCS3V3,toGPS FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,toADCS5V,toADCS3V3,toGPS\n");
    foreach ($conn->query($sql) as $row)
    {
      echo ($row['Date'].",".(int)$row['toADCS5V'].",".(int)$row['toADCS3V3']
      .",".(int)$row['toGPS']."\n");
    }
}

function getTemp($conn,$sID)
{
    $sql = 'SELECT Da'.'te, BatTemp,Convert1temp,Convert2temp,Convert3temp FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,Bat_Temp,Converter1_Temp,Converter2_Temp,Converter3_Temp\n");
    foreach ($conn->query($sql) as $row)
    {
        echo ($row['Date'].",".(int)$row['BatTemp'].",".(int)$row['Convert1temp']
        .",".(int)$row['Convert2temp'].",".(int)$row['Convert3temp']."\n");
    }
}

?>
