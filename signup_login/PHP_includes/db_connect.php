<?php
$db_connect = mysqli_connect("localhost","skcnca_admin", "hrl930422", "skcnca_spotposting");
//Evaluate the connection
if(mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
}
else {
    echo "Successful database connection, happy coding!!!";
}
?>