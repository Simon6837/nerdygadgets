<?php
// An API that returns the current temperature of cold products every time it is called

include_once "database.php";
$databaseConnection = connectToDatabase();

// get temperature from API
$measurement = file_get_contents('http://tiemco.windybois.nl:8080/insidetemp');

// check if there is already a temperature with ColdRoomSensorNumber 5
$checkQuery = "SELECT * FROM coldroomtemperatures WHERE ColdRoomSensorNumber = 5";
$result = mysqli_query($databaseConnection, $checkQuery);
$temperature_old = mysqli_fetch_assoc($result);

if ($measurement) {
    if (mysqli_num_rows($result) > 0) {
        // archive the existing measurement
        $archiveQuery = "INSERT INTO coldroomtemperatures_archive (ColdRoomTemperatureID, ColdRoomSensorNumber, RecordedWhen, Temperature, ValidFrom, ValidTo) VALUES (?, 5, ?, ?, ?, ?)";
        $archiveStatement = mysqli_prepare($databaseConnection, $archiveQuery);
        mysqli_stmt_bind_param($archiveStatement, 'issss', $temperature_old['ColdRoomTemperatureID'], $temperature_old['RecordedWhen'], $temperature_old['Temperature'], $temperature_old['ValidFrom'], $temperature_old['ValidTo']);
        mysqli_stmt_execute($archiveStatement);

        // delete the existing measurement from the non-archive table
        mysqli_query($databaseConnection, "DELETE FROM coldroomtemperatures WHERE ColdRoomSensorNumber = 5");
    }
    // Gets current date
    $date = date('Y-m-d H:i:s');
    // Gets current date and then adds a specific amount of time
    $dateW = date('Y-m-d', strtotime(date('Y-m-d') . '+1 week'));

    $temperature = json_decode($measurement, true)['temperature'];

    // insert temperature into database
    $sql = "INSERT INTO coldroomtemperatures (ColdRoomSensorNumber, RecordedWhen, Temperature, ValidFrom, ValidTo) VALUES (5, ?, $temperature, ?, ?)";
    $statement = mysqli_prepare($databaseConnection, $sql);
    mysqli_stmt_bind_param($statement, 'sss', $date, $date, $dateW);
    mysqli_stmt_execute($statement);
} else {
    // if the API request fails, return the last known temperature if it exists
    $temperature = $temperature_old['Temperature'] ?? 0;
}


// send the temperature to the page
$response = [
    "success" => true,
    "data" => $temperature
];

echo json_encode($response);
