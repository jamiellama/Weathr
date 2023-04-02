<?php 
include "dbconn.php";

$api_key_value = "jpETCif5i45C5aDjBGiH";
$api_key = "";
$outdoor_temperature = "";
$outdoor_humidity = "";
$outdoor_pressure = "";

$api_key = $_POST["apiKey"];
    
if($api_key == $api_key_value)
{
    $outdoor_temperature = number_format($_POST["outdoorTemp"], 1);
    $outdoor_humidity = $_POST["outdoorHumid"];
    $outdoor_pressure = $_POST["outdoorPress"];

    echo "Received data successfully!";

    $sql = "INSERT INTO BMESensor (OutdoorTemperature, OutdoorHumidity, OutdoorPressure)
    VALUES ($outdoor_temperature, $outdoor_humidity, $outdoor_pressure)";

    if (mysqli_query($conn, $sql))
    {
        //echo "data inserted into database";
    }
    else
    {
        //echo "SQL error: " . $sql . "<br>" . mysqli_error($conn);
    }

    $mysqli_close($conn);
}
else
{
    //echo "API keys do not match";
}
?>