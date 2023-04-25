// Connect to Redis server
$redis = new Redis();
$redis->connect('192.168.0.127', 9400, 'isHahn8k)aQ~e5X');

// MySQL query
$query = "SELECT DISTINCT TADIGCode,MCC,MNC,CC FROM xml";

// Check if result is present in Redis cache
$result = $redis->get($query);

if (!$result) {
    // Execute MySQL query if result is not present in Redis cache
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        // Error handling
        printf("Error: %s\n", mysqli_error($con));
        exit();
    }
    
    // Store result in Redis cache with expiry time
    $redis->set($query, json_encode($result));
    $redis->expire($query, 3600); // Expiry time in seconds
} else {
    // Retrieve result from Redis cache if present
    $result = json_decode($result);
}

// Display result
while ($row = mysqli_fetch_array($result)) {
    echo $row['TADIGCode'] . "<br>";
}

// Delete Redis cache entry when updating or deleting data in MySQL
$redis->del($query);

