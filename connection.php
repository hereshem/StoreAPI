<?php
$connect=mysqli_connect('localhost','root','','hem_store');
if(!$connect){
	die("Connection error ".mysqli_connect_error());
}
?>