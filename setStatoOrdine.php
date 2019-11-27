<?php

	include "connessione.php";
	include "Session.php";
	
	$ordine_fornitore=$_REQUEST['ordine_fornitore'];
	$colonna=$_REQUEST['colonna'];
	$valore=$_REQUEST['valore'];
	
	$query2="UPDATE [dbo].[registrazioni_ricevimento_merci] SET [$colonna]='$valore' WHERE [ordine_acquisto] = '$ordine_fornitore'";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));*/
		die('error');
	}
	else
	{
		if($colonna=='controllato' && $valore=='false')
		{
			$query3="UPDATE [dbo].[registrazioni_ricevimento_merci] SET [completato]='false',[destinazione]=NULL WHERE [ordine_acquisto] = '$ordine_fornitore'";
			$result3=sqlsrv_query($conn,$query3);
			if($result3==FALSE)
			{
				/*echo "<br><br>Errore esecuzione query<br>Query: ".$query3."<br>Errore: ";
					die(print_r(sqlsrv_errors(),TRUE));*/
				die('error');
			}
			else
				echo "ok";
		}
		else
			echo "ok";
	}

?>