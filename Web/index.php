<?php include "dbconn.php";
header("Refresh:60");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="icon" href="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png">
    <style type="text/css">
        body {
            font-family: "Trebuchet MS", Arial;
        }

        @media (min-width: 576px) {
            .h-sm-100 {
            height: 100%;
            }
        }

        .nav-link {
            color: white;
        }

        .nav-link:hover {
            color: #E8E8E8;
        }

        .content {
            max-width: 1000px;
            margin: auto;
        }

        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 15%;
        }

        .title {
            text-align: center;
        }

        .card {
            transition: 0.2s;
        }

        #card-hover:hover {
            background: rgb(25, 135, 84);
            color: white;
            transition: 0.2s;
        }

        #system-status {
            background: #F5F5F5;
        }

        #major {
            color: rgb(255,140,0);
        }

        #minor {
            color: rgb(244,202,22);
        }
    </style>
</head>


<div class="container-fluid overflow-hidden">
    <div class="row vh-100 overflow-auto">
        <div class="col-12 col-sm-3 col-xl-2 px-sm-2 px-0 bg-success d-flex sticky-top">
            <div class="d-flex flex-sm-column flex-row flex-grow-1 align-items-center align-items-sm-start px-3 pt-2 text-white">
                <a href="/index.php" class="d-flex align-items-center pb-sm-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <img src="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png" class="d-none d-md-block" alt="RPi-Smart-Garden-Logo" width="110px" height="110px">
                    <img src="https://i.ibb.co/mtFCL4R/RPi-Logo-Round.png" class="d-md-none" alt="RPi-Smart-Garden-Logo" width="30px" height="30px">
                </a>
                <ul class="nav nav-pills flex-sm-column flex-row flex-nowrap flex-shrink-1 flex-sm-grow-0 flex-grow-1 mb-sm-auto mb-0 justify-content-center align-items-center align-items-sm-start" id="menu">
                    <li class="nav-item">
                        <a href="/index.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-house-door"></i><span class="ms-1 d-none d-sm-inline">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/visuals.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-bar-chart-line"></i><span class="ms-1 d-none d-sm-inline">Visuals</span></a>
                    </li>
                    <li>
                        <a href="/leaf.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-chat-dots"></i><span class="ms-1 d-none d-sm-inline">Leaf</span></a>
                    </li>
                    <li>
                        <a href="/data.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-file-earmark-spreadsheet"></i><span class="ms-1 d-none d-sm-inline">Data Log</span></a>
                    </li>
                    <li>
                        <a href="/info.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-info-circle"></i><span class="ms-1 d-none d-sm-inline">Info and Support</span></a>
                    </li>
                    <li>
                        <a href="/settings.php" class="nav-link px-sm-0 px-2">
                            <i class="fs-5 bi-gear"></i><span class="ms-1 d-none d-sm-inline">Settings</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <body>
            <?php
                $sql = "SELECT * FROM sensor_data ORDER BY data_id DESC LIMIT 1";
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_array($result)) {
                    $timestamp = $row['timestamp'];
                    $soil_moisture = $row['soil_moisture'];
                    $temperature = $row['temperature'];
                    $humidity = $row['humidity'];
                    $light_level = $row['light_level'];
                    $water_tank_level = $row['water_tank_level'];

                    $minor_alert = FALSE;
                    $major_alert = FALSE;
                    $critical_alert = FALSE;
                    
                    $alert_water_tank_empty = FALSE;
                    $alert_water_tank_low = FALSE;
                    $alert_temperature_high = FALSE;
                    $alert_temperature_very_high = FALSE;
                    $alert_temperature_low = FALSE;
                    $alert_temperature_very_low = FALSE;
                    $alert_no_recent_data = FALSE;


                    // Water tank empty alert
                    if ($water_tank_level <= 15) {
                        $critical_alert = TRUE;
                        $alert_water_tank_empty = TRUE;
                    }

                    // Water tank low alert
                    if ((16 <= $water_tank_level) && ($water_tank_level <= 30)) {
                        $major_alert = TRUE;
                        $alert_water_tank_low = TRUE;
                    }

                    // Temperature high alert
                    if ((26 <= $temperature) && ($temperature <=28)) {
                        $minor_alert = TRUE;
                        $alert_temperature_high = TRUE;
                    }

                    // Temperature very high alert
                    if ($temperature >= 29) {
                        $major_alert = TRUE;
                        $alert_temperature_very_high = TRUE;
                    }

                    // Temperature low alert
                    if ((15 <= $temperature) && ($temperature <=17)) {
                        $minor_alert = TRUE;
                        $alert_temperature_low = TRUE;
                    }

                    // Temperature very low alert
                    if ($temperature <= 14) {
                        $major_alert = TRUE;
                        $alert_temperature_very_low = TRUE;
                    }

                }
                $datetimestr = strtotime($timestamp);
                $formatdatetime = date("d/m/y g:i:s A", $datetimestr);
                $time_difference = (strtotime("now")-strtotime($timestamp));
                $time_difference_minutes_raw = ($time_difference / 60);
                $time_difference_minutes = round($time_difference_minutes_raw);
                
                $water_usage_query = "SELECT SUM(water_dispensed) AS daily_total FROM sensor_data WHERE DATE(`timestamp`) = CURDATE()";
                $water_usage_query_result = mysqli_query($conn, $water_usage_query);

                while ($row = mysqli_fetch_array($water_usage_query_result)) {
                    $daily_water_usage = $row['daily_total'];
                }

                if ($daily_water_usage == 0) {
                    $daily_water_usage = 0;
                }

                // Alert testing
                //$critical_alert = TRUE;
                //$major_alert = TRUE;
                //$minor_alert = TRUE;

                //$alert_water_tank_empty = TRUE;
                //$alert_water_tank_low = TRUE;
                //$alert_temperature_high = TRUE;
                //$alert_temperature_very_high = TRUE;
                //$alert_temperature_low = TRUE;
                //$alert_temperature_very_low = TRUE;

                ?>
            
            <div class="col py-3">
                <div class="row">
                </div>
                <div class="row">
                    <h2>J.A.R.V.I.S. Raspberry Pi Smart Garden</h2>
                </div>
                <div class="row">
                    <h4>Dashboard</h4>
                </div>
                <div class="row">
                    <?php
                    $last_updated = "Last Updated: $formatdatetime ";

                    if ($time_difference_minutes == 0) {
                        $last_updated .= "(less than a minute ago)";
                    } elseif ($time_difference_minutes == 1) {
                        $last_updated .= "($time_difference_minutes minute ago)";
                    } else {
                        $last_updated .= "($time_difference_minutes minutes ago)";
                    }

                    echo"<p class='text-secondary'>$last_updated</p>";
                    ?>
                </div>
                <?php
                if ($time_difference >= 3600) {
                    $critical_alert = TRUE;
                    $alert_no_recent_data = TRUE;
                    echo"   <div class='alert alert-danger' role='alert'>
                            No recent data has been received from the RPi Smart Garden! Go to <a href='/info.php' class='alert-link'>Info and Support</a> for troubleshooting.
                            </div> ";
                }
                ?>
                <div class="row" data-masonry='{"percentPosition": true }'>
                    <div class="col-sm-4 col-md-3 py-3">
                        <div class="card border-success text-center" id="card-hover">
                            <div class="card-body">
                                <i class="fs-4 bi-droplet"></i>
                                <h4 class="card-title">Soil Moisture:</h4>
                                <?php echo"<h3 style='display:inline;' class='card-text'>$soil_moisture</h3>" ?>
                                <h3 style="display:inline;">%</h3>
                                <a href="/visuals.php#soil_moisture_section" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-4 col-md-3 py-3">
                        <div class="card border-success text-center" id="card-hover">
                            <div class="card-body">
                                <i class="fs-4 bi-thermometer-half"></i>
                                <h4 class="card-title">Temperature:</h4>
                                <?php echo"<h3 style='display:inline;' class='card-text'>$temperature</h3>" ?>
                                <h3 style="display:inline;">°C</h3>
                                <a href="/visuals.php#temperature_section" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3 py-3">
                        <div class="card border-success text-center" id="card-hover">
                            <div class="card-body">
                                <i class="fs-4 bi-water"></i>
                                <h4 class="card-title">Humidity:</h4>
                                <?php echo"<h3 style='display:inline;' class='card-text'>$humidity</h3>" ?>
                                <h3 style="display:inline;">%</h3>
                                <a href="/visuals.php#humidity_section" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3 py-3">
                        <div class="card border-success text-center" id="card-hover">
                            <div class="card-body">
                                <i class="fs-4 bi-brightness-high"></i>
                                <h4 class="card-title">Light Level:</h4>
                                <?php echo"<h3 style='display:inline;' class='card-text'>$light_level</h3>" ?>
                                <h3 style="display:inline;">Lux</h3>
                                <a href="/visuals.php#light_level_section" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3 py-3">
                        <div class="card border-success text-center" id="card-hover">
                            <div class="card-body">
                                <i class="fs-4 bi-moisture"></i></i>
                                <h4 class="card-title">Water Tank Level:</h4>
                                <?php echo"<h3 style='display:inline;' class='card-text'>$water_tank_level</h3>" ?>
                                <h3 style="display:inline;">%</h3>
                                <a href="/visuals.php#water_tank_level_section" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-3 py-3">
                        <div class="card border-success text-center" id="card-hover">
                            <div class="card-body">
                                <i class="fs-4 bi-clock-history"></i>
                                <h4 class="card-title">Daily Water Usage:</h4>
                                <?php echo"<h3 style='display:inline;' class='card-text'>$daily_water_usage</h3>" ?>
                                <h3 style="display:inline;">ml</h3>
                                <a href="/visuals.php#water_usage_section" class="stretched-link"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 col-md-6 py-3">
                        <div class="card border-success text-center" id="system-status">
                            <div class="card-body">
                                <i class="fs-4 bi-question-octagon"></i>
                                <h4 class="card-title">System Status:</h4>
                                <?php
                                if ($critical_alert == TRUE) {
                                    echo "<h4 class='card-text text-danger' id='critical'>Critical system alert - view below!</h4>";
                                } elseif ($major_alert == TRUE) {
                                    echo "<h4 class='card-text' id='major'>Major system alert - view below!</h4>";
                                } elseif ($minor_alert == TRUE) {
                                    echo "<h3 class='card-text' id='minor'>Minor system alert - view below!</h3>";
                                } else {
                                    echo "<h3 class='card-text text-success'>All good! No known issues.</h3>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row px-2 py-3">
                    <?php
                    // Water tank empty alert (critical)
                    if ($alert_water_tank_empty == TRUE) {
                        echo   "<div class='alert alert-danger' role='alert'>
                                    <h4 class='alert-heading'>Water tank level alert - urgent action required!</h4>
                                    The water tank is empty and needs filled as soon as possible. To prevent pump damage, the RPi Smart Garden will not be watered
                                    until the tank is full. <a href='/info.php' class='alert-link'>Learn more</a>
                                    <p>To fill the water tank:</p>
                                    <ol>
                                        <li>Carefully unclip the water level sensor and move it aside</li>
                                        <li>Insert the funnel into the tank to protect electronics</li>
                                        <li>Fill the tank with cool water until the level reaches the max fill line</li>
                                        <li>Remove the funnel and reclip the water level sensor back onto the top of the tank</li>
                                    </ol>
                                </div>";
                    }

                    // No recent data alert (critical)
                    if ($alert_no_recent_data == TRUE) {
                        echo    "<div class='alert alert-danger' role='alert'>
                        <h4 class='alert-heading'>No recent data received alert - urgent action required!</h4>
                        No recent data has been received from the RPi Smart Garden. It is recommended to reboot the Raspberry Pi as soon as possible. <a href='/info.php' class='alert-link'>Learn more</a>
                    </div>";
                    }

                    // Water tank low (major)
                    if ($alert_water_tank_low == TRUE) {
                        echo   "<div class='alert alert-warning' role='alert'>
                        <h4 class='alert-heading'>Water tank level alert - action required!</h4>
                        The water tank level is low and needs to be filled as soon as possible. <a href='/info.php' class='alert-link'>Learn more</a>
                        <p>To fill the water tank:</p>
                        <ol>
                            <li>Carefully unclip the water level sensor and move it aside</li>
                            <li>Insert the funnel into the tank to protect electronics</li>
                            <li>Fill the tank with cool water until the level reaches the max fill line</li>
                            <li>Remove the funnel and reclip the water level sensor back onto the top of the tank</li>
                        </ol>
                    </div>";
                    }

                    // Temperature high (minor)
                    if ($alert_temperature_high == TRUE) {
                        echo    "<div class='alert alert-warning' role='alert'>
                        <h4 class='alert-heading'>Temperature alert!</h4>
                        The RPi Smart Garden's ambient temperature is slightly higher than the optimal range, this may affect plant growth. 
                        It is recommended to take action such as opening a window. <a href='/info.php' class='alert-link'>Learn more</a>
                    </div>";
                    }

                    // Temperature very high (major)
                    if ($alert_temperature_very_high == TRUE) {
                        echo    "<div class='alert alert-warning' role='alert'>
                        <h4 class='alert-heading'>Temperature alert - action required!</h4>
                        The RPi Smart Garden's ambient temperature is much higher than the optimal range, this will affect plant growth. 
                        Please take action such as opening a window or moving the RPi Smart Garden to a cooler location if possible. <a href='/info.php' class='alert-link'>Learn more</a>
                    </div>";
                    }

                    // Temperature low (minor)
                    if ($alert_temperature_low == TRUE) {
                        echo    "<div class='alert alert-warning' role='alert'>
                        <h4 class='alert-heading'>Temperature alert!</h4>
                        The RPi Smart Garden's ambient temperature is slightly lower than the optimal range, this may affect plant growth. 
                        It is recommended to take action such as turning up the heating. <a href='/info.php' class='alert-link'>Learn more</a>
                    </div>";
                    }

                    // Temperature very low (major)
                    if ($alert_temperature_very_low == TRUE) {
                        echo    "<div class='alert alert-warning' role='alert'>
                        <h4 class='alert-heading'>Temperature alert - action required!</h4>
                        The RPi Smart Garden's ambient temperature is much lower than the optimal range, this will affect plant growth. 
                        Please take action such as turning up the heating or moving the RPi Smart Garden to a warmer location if possible. <a href='/info.php' class='alert-link'>Learn more</a>
                    </div>";
                    }
                    ?>
                </div>

            </div>
        </body>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"
    integrity="sha384-GNFwBvfVxBkLMJpYMOABq3c+d3KnQxudP/mGPkzpZSTYykLBNsZEnG2D9G/X/+7D" crossorigin="anonymous"
    async></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"
    integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk"
    crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js"
    integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK"
    crossorigin="anonymous"></script>
</body>

</html>