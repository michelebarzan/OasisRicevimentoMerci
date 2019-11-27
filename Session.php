<?php
session_start();
if(!isset($_SESSION['Username']))
{
	
	header("Location: autenticazione1.php");
}
?>