<?php
 // SQL Database Connection
 
 $host = "localhost";
 $user = "jamie44lar_weathrAdm1n";
 $pw = "mHFkTJkDDs";
 $db = "jamie44lar_weathr";

 // database connection check
 $conn = new mysqli($host, $user, $pw, $db);

 if ($conn->connect_error) {
    echo  "not connected to database".$conn->connect_error;
    exit();
 } else {
    //echo "connected to database";
 }

 ?>