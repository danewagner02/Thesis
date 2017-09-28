<?php

$i = 0;
$file = fopen("Satellites.csv", "r");

while (!feof($file)) {
    $satelliteNames[$i] = fgetcsv($file)[1];
    $i++;
}


fclose($file);

/**
 * This function is used to display satellites
 * for the serverside paging webservice.
 **/
$skip = 0;
if (isset($_GET['skip'])) {
    $skip  = intval($_GET['skip']);
}
$limit = 12;
if (isset($_GET['limit'])) {
    $limit = intval($_GET['limit']);
    if ($limit > 12 || $limit < 1) {
        $limit = 12;
    }
}

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');

echo '{';
echo '  "total": '.sizeof($satelliteNames).',';
echo '  "satellites": [';
for ($i = $skip; ($i - $skip < $limit) && ($i < sizeof($satelliteNames)); $i++ ) {
    echo '{';
    echo '  "id": '.$i.',';
    echo '  "name": "'.$satelliteNames[$i].'",';
    echo '  "url": "images/'.$satelliteNames[$i].'.jpg"';
    echo '}';
    if ($i - $skip < $limit - 1 && $i < sizeof($satelliteNames) - 1) {
        echo ',';
    }
}
echo '  ]';
echo '}';

?>
