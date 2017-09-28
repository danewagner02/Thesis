<?php
$servername = "localhost";
$username = "Admin";
$password = "pass";
$voltagedata = array();
$currentINdata = array();
$currentOutdata = array();
$sID = "sat0";
if (isset($_GET['sID'])) {
    $sID  = $_GET['sID'];
    //echo ("is set");
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=thesisDB", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"."\n";
    //echo json_encode("Connected!");
    getVoltage($conn,$sID);
    getCurrIn($conn,$sID);
    getCurrOut($conn,$sID);
    echo json_encode($voltagedata);
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }


function getVoltage($conn,$sID)
{
    $sql = 'SELECT logTime, voltage FROM sats WHERE ID="'.$sID.'"';
    foreach ($conn->query($sql) as $row)
    {
        $tempArr = $row['logTime'].",".$row['voltage'];
        array_push($GLOBALS['voltagedata'] , $tempArr );
    }
}

function getCurrIn($conn,$sID)
{
    $sql = 'SELECT logTime, currentIN FROM sats WHERE ID="'.$sID.'"';
    foreach ($conn->query($sql) as $row)
    {
        $tempArr = array("".$row['logTime'],"".$row['currentIN']);
        array_push($GLOBALS['currentINdata'] , $tempArr );
    }
}

function getCurrOut($conn,$sID)
{
    $sql = 'SELECT logTime, currentOut FROM sats WHERE ID="'.$sID.'"';
    foreach ($conn->query($sql) as $row)
    {
        $tempArr = array("".$row['logTime'],"".$row['currentOut']);
        array_push($GLOBALS['currentOutdata'] , $tempArr );
    }
}

?>
