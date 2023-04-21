<?php
// Connect to database
// Server - localhost
// Username - root
// Password - empty
// Database name = ir21
$conn = mysqli_connect("localhost", "root", "", "ir21");

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$affectedRows = 0;
$errorMessages = [];

// Load organizationinformation file else check connection
$xml = simplexml_load_file("deue2.xml");

// Check if file was loaded successfully
if ($xml === false) {
    die("Error: Cannot create object");
}

// Assign values
foreach ($xml->children() as $row) {
    $OrganisationName = $row->OrganisationName;
    $CountryInitials = $row->CountryInitials;
    $TADIGCode = $row->NetworkList->Network->TADIGCode;
    $NetworkName = $row->NetworkList->Network->NetworkName;
    $NetworkType = $row->NetworkList->Network->NetworkType;
    $MCC = $row->NetworkList->Network->NetworkData->RoutingInfoSection->RoutingInfo->CCITT_E212_NumberSeries->MCC;
    $MNC = $row->NetworkList->Network->NetworkData->RoutingInfoSection->RoutingInfo->CCITT_E212_NumberSeries->MNC;
    $MGT_CC = $row->NetworkList->Network->NetworkData->RoutingInfoSection->RoutingInfo->CCITT_E214_MGT->MGT_CC;
    $MGT_NC = $row->NetworkList->Network->NetworkData->RoutingInfoSection->RoutingInfo->CCITT_E214_MGT->MGT_NC;
    $CC = $row->NetworkList->Network->NetworkData->RoutingInfoSection->RoutingInfo->CCITT_E164_NumberSeries->MSISDN_NumberRanges->RangeData->NumberRange->CC;

    // Get all the NDC values from the RangeData elements and insert them into the database
    foreach ($row->NetworkList->Network->NetworkData->RoutingInfoSection->RoutingInfo->CCITT_E164_NumberSeries->MSISDN_NumberRanges->RangeData as $rangeData) {
        $NDC = $rangeData->NumberRange->NDC;

        // SQL query to insert data into organizationinformation table
        $sql = "INSERT INTO xml(OrganisationName, CountryInitials, TADIGCode, NetworkName, NetworkType, MCC, MNC,MGT_CC,MGT_NC,CC,NDC) VALUES ('" . $OrganisationName . "',' " .$CountryInitials . "','" . $TADIGCode . "','" . $NetworkName . "','" . $NetworkType . "','" . $MCC . "','" . $MNC . "','" .$MGT_CC ."','" .$MGT_NC  ."','" . $CC . "', '" . $NDC . "')";

        $result = mysqli_query($conn, $sql);

        if (!empty($result)) {
            $affectedRows++;
        } else {
            $errorMessages[] = mysqli_error($conn);
        }
    }
}

// Close database connection
mysqli_close($conn);

// Output result message
if ($affectedRows > 0) {
    $message = "Data imported successfully! Total $affectedRows rows were inserted.";
} else {
    $message = "No data imported!";
}

?>