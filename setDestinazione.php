<?php

	include "connessione.php";
	include "Session.php";
	
	$ordine_fornitore=$_REQUEST['ordine_fornitore'];
	$destinazione=$_REQUEST['destinazione'];
	
	$query2="UPDATE [dbo].[registrazioni_ricevimento_merci] SET [destinazione]='$destinazione' WHERE [ordine_acquisto] = '$ordine_fornitore'";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));*/
		die('error');
	}
	else
		echo "ok";

?>