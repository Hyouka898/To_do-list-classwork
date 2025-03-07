<?php
    $conn = new mysqli('localhost','root','','to_do_list');
    if(mysqli_connect_error()){
       echo die("error connect data base");
    }
?>