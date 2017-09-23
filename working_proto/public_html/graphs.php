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


    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }


function getVoltage($conn,$sID)
{
    $sql = 'SELECT logTime, voltage FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,Voltage\n");
    foreach ($conn->query($sql) as $row)
    {
        //$tempArr = array((int)$row['logTime'],floatval($row['voltage']));
        //array_push($GLOBALS['voltagedata'] , $tempArr );
        echo (((int)$row['logTime'].",".floatval($row['voltage'])."\n"));

    }
}

function getCurr($conn,$sID)
{
    $sql = 'SELECT logTime, currentIN, currentOut FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,Current_In,Current_Out,Current_NET\n");
    foreach ($conn->query($sql) as $row)
    {
        //$tempArr = array((int)$row['logTime'],floatval($row['currentIN'] ));
        //array_push($GLOBALS['currentINdata'] ,$tempArr );
        $inCurr = floatval($row['currentIN']);
        $outCurr = floatval($row['currentOut']);
        $netCurr = $inCurr - $outCurr;
        echo (((int)$row['logTime'].",".floatval($row['currentIN']).",".floatval($row['currentOut']).",".$netCurr."\n"));
    }
}

function getCurrOut($conn,$sID)
{
    $sql = 'SELECT logTime, currentOut FROM sats WHERE ID="'.$sID.'"';
    echo ("Time,CurrentOut\n");
    foreach ($conn->query($sql) as $row)
    {
        //$tempArr = array((int)$row['logTime'],floatval($row['currentOut']) );
        //array_push($GLOBALS['currentOutdata'] , $tempArr);
        echo (((int)$row['logTime'].",".floatval($row['currentOut'])."\n"));
    }
}

?>
