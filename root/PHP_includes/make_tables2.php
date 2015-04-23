<?php
include_once("db_connect.php");

//Create status table

$tbl_status = "CREATE TABLE IF NOT EXISTS status (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    osid INT(11) NOT NULL,
                    account_name VARCHAR(16) NOT NULL,
                    author VARCHAR(16) NOT NULL,
                    type ENUM('a','b','c') NOT NULL,
                    data TEXT NOT NULL,
                    postdate DATETIME NOT NULL,
                    PRIMARY KEY (id)
               )";

$query = mysqli_query($db_connect, $tbl_status);
if ($query === TRUE){
    echo "<h3>status table created OK</h3>";
}
else{
    echo "<h3>status table NOT created</h3>";
}

?>