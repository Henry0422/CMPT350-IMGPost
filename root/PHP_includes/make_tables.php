<?php
include_once("db_connect.php");



//Create friends users
$tbl_users = "CREATE TABLE users (
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(16) NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                gender ENUM('m','f') NOT NULL,
                website VARCHAR(255) NULL,
                userlevel ENUM('a','b','c','d') NOT NULL DEFAULT 'a',
                avatar VARCHAR(255) NULL,
                ip VARCHAR(255) NOT NULL,
                signup DATETIME NOT NULL,
                lastlogin DATETIME NOT NULL,
                notescheck DATETIME NOT NULL,
                activatied ENUM('0','1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id),
                UNIQUE KEY username (username,email)
             )";

$query = mysqli_query($db_connect, $tbl_users);
if ($query === TRUE){
    echo "<h3>users table created OK</h3>";
}
else{
    echo "<h3>users table NOT created</h3>";
}



//Create useroptions table
$tbl_useroptions = "CREATE TABLE IF NOT EXISTS useroptions (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    username VARCHAR(16) NOT NULL,
                    background VARCHAR(255) NOT NULL,
                    question VARCHAR(255) NULL,
                    answer VARCHAR(255) NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY username (username)
                   )";

$query = mysqli_query($db_connect, $tbl_useroptions);
if ($query === TRUE){
    echo "<h3>useroptions table created OK</h3>";
}
else{
    echo "<h3>useroptions table NOT created</h3>";
}



//Create friends table
//friends request from user1
//once user2 accept it, accepted will be 1
$tbl_friends = "CREATE TABLE IF NOT EXISTS friends (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user1 VARCHAR(16) NOT NULL,
                user2 VARCHAR(16) NOT NULL,
                datemade DATETIME NOT NULL,
                accepted ENUM('0','1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                )";

$query = mysqli_query($db_connect, $tbl_friends);
if ($query === TRUE){
    echo "<h3>friends table created OK</h3>";
}
else{
    echo "<h3>friends table NOT created</h3>";
}


//Create blockusers table
$tbl_blockedusers = "CREATE TABLE IF NOT EXISTS blockedusers (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    blocker VARCHAR(16) NOT NULL,
                    blockee VARCHAR(16) NOT NULL,
                    blockdate DATETIME NOT NULL,
                    PRIMARY KEY (id)
                    )";

$query = mysqli_query($db_connect, $tbl_blockedusers);
if ($query === TRUE){
    echo "<h3>blockedusers table created OK</h3>";
}
else{
    echo "<h3>blockedusers table NOT created</h3>";
}



//Create status table

//$tbl_status = "CREATE TABLE IF NOT EXISTS status (
//                    id INT(11) NOT NULL AUTO_INCREMENT,
//               )";



//Create photos table
$tbl_photos = "CREATE TABLE IF NOT EXISTS photos (
                id INT(11) NOT NULL AUTO_INCREMENT,
                gallery VARCHAR(16) NOT NULL,
                filename VARCHAR(255) NOT NULL,
                description VARCHAR(255) NULL,
                uploaddate DATETIME NOT NULL,
                PRIMARY KEY (id)
              )";

$query = mysqli_query($db_connect, $tbl_photos);
if ($query === TRUE){
    echo "<h3>photos table created OK</h3>";
}
else{
    echo "<h3>photos table NOT created</h3>";
}



//Create notifications table
$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications (
                        id INT(11) NOT NULL AUTO_INCREMENT,
                        username VARCHAR(16) NOT NULL,
                        initiator VARCHAR(16) NOT NULL,
                        app VARCHAR(255) NOT NULL,
                        note VARCHAR(255) NOT NULL,
                        did_read ENUM('0','1') NOT NULL DEFAULT '0',
                        date_time DATETIME NOT NULL,
                        PRIMARY KEY (id)
                      )";

$query = mysqli_query($db_connect, $tbl_notifications);
if ($query === TRUE){
    echo "<h3>notifications table created OK</h3>";
}
else{
    echo "<h3>notifications table NOT created</h3>";
}


?>