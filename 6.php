<?php
require './vendor/autoload.php';

$redis = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => '192.168.0.127',
    'port'   => 9400,
    'password' => 'isHahn8k)aQ~e5X',
]);

$cachedEntry = $redis->get('xml');

if($cachedEntry){
    echo "From Redis Cache";
    echo $cachedEntry;
    exit();
} else {
    $conn = new mysqli('localhost:3306', 'root', '', 'ir21');
    $sql = "SELECT `TADIGCode`,`MCC`,`MNC` FROM `xml`";
    $result = $conn->query($sql);
    echo "From Database.<br>";
    // remove the empty key from Redis
    $redis->del('xml');
    $temp = array();
    while($row = $result->fetch_assoc()){
        $tadigCode = "TC:".$row['TADIGCode'];
        $mnc = $row['MNC'];
        $mcc = $row['MCC'];
        $t1 = $mcc.':'.$mnc;
        $redis->set($tadigCode, $t1);
        $temp[] = $tadigCode.' '.$t1;
    }
    echo "Data saved to Redis.";
    $redis->set('xml', implode('<br>', $temp));
    exit();
}
?>
