<?php
require './vendor/autoload.php';
$redis =new Predis\Client();
$redis->connect('192.168.0.127', 9400, 'isHahn8k)aQ~e5X');
$cachedEntry = $redis->get('xml');


if($cachedEntry){
    echo "From Redis Cache";
    echo $cachedEntry;
    exit();
}

else{
    $conn =new mysqli('localhost:3306','root','','ir21');
    $sql="SELECT `TADIGCode`,`MCC`,`MNC`,`CC` FROM `xml`;";
    $result = $conn->query($sql);
    echo "From Database.''<br/>";
    while($row =$result->fetch_assoc()){
        echo "TC:";
        echo $row['TADIGCode'].' ';
        echo $row['MCC'].'';
        echo $row['MNC'].'';
        echo $row['CC'].'';
        $temp .= " TC:".$row['TADIGCode'].' '.$row['MCC'].' '.$row['MNC'].' '.$row['CC'] .'<br>';
    }
    $redis->set('xml',$temp);
    exit();
}