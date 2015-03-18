<?php
include_once("php_includes/check_login_status.php");
// Initialize any variables that the page might echo
$u = "test111";
$sex = "";
$userlevel = "";
$joindate = "";
$lastsession = "";
// Make sure the _GET username is set, and sanitize it
//if(isset($_GET["u"])){
//	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
//} 
//else {
//    header("location: http://www.skchn.ca/index.html");
//    exit();	
//}
// Select the member from the users table
$sql = "SELECT * FROM users WHERE username='".$u."' AND activated='1' LIMIT 1";
$user_query = mysqli_query($db_connect, $sql);
if($user_query->num_rows >0){
    while($row = $user_query->fetch_assoc()){
    echo "<tr>
        <td>".$row["id"]."</td>
        <td>".$row["username"]." </td>
        <td>".$row["email"]."</td>
        <td>".$row["password"]."</td>
    </tr>";
    }
}
else{
    echo "0 result";
}
?>