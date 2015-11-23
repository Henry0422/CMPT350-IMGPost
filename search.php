<?php
mysql_connect("localhost","skcnca_admin", "hrl930422" ) or die("Could not connect!");
mysql_select_db("skcnca_spotposting") or die("could not find db!");
$output = '';
if(isset($_POST['search'])) {
    $searchq = $_POST['search'];
    $searchq = preg_replace("#[^0-9a-z]#i","",$searchq);
    
    $query = mysql_query("SELECT * From users WHERE username LIKE '%$searchq%' ") or die("could not search!");
    $count = mysql_num_rows($query);
    if($count == 0){
        $output = 'There was no search results!';
    }
    else{
        while($row = mysql_fetch_array($query)) {
            $uname = $row['username'];
            $id = $row['id'];
            
            $output .= '<a href="user.php?u='.$uname.'">'.$uname.'</a></br>';
        }
    }
}
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Search</title>
<script>
    function showResult(str) {
      if (str.length==0) { 
        document.getElementById("livesearch").innerHTML="";
        document.getElementById("livesearch").style.border="0px";
        return;
      }
      if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
      } else {  // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
          document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
          document.getElementById("livesearch").style.border="1px solid #A5ACB2";
        }
      }
      xmlhttp.open("GET","livesearch.php?q="+str,true);
      xmlhttp.send();
    }
</script>
</head>
<body>
    <form action="search.php" method="post">
        <input type="text" name="search" size="30" placeholder="Search for users..." onkeyup="showResult(this.value)" />
        <input type="submit" value="Search" />
        <div style="width:194px" id="livesearch"></div>
    </form>
    
    <?php print("$output");?>
    
</body>
</html>