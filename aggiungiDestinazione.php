<?php

	include "connessione.php";
	include "Session.php";
	
	$destinazione=$_REQUEST['destinazione'];
	$descrizione=$_REQUEST['descrizione'];
	
	$destinazione=str_replace("'","''",$destinazione);
	$descrizione=str_replace("'","''",$descrizione);

	$query2="INSERT INTO [dbo].[destinazioni_ricevimento_merci]
				   ([destinazione]
				   ,[descrizione])
			 VALUES
				   ('$destinazione'
				   ,'$descrizione')";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));*/
		die('error');
	}
	else
	{
		$query1="SELECT id_destinazione FROM [dbo].[destinazioni_ricevimento_merci] WHERE destinazione='$destinazione' AND descrizione='$descrizione'";
		$result1=sqlsrv_query($conn,$query1);
		if($result1==FALSE)
		{
			/*echo "<br><br>Errore esecuzione query<br>Query: ".$query1."<br>Errore: ";
				die(print_r(sqlsrv_errors(),TRUE));*/
			die('error');
		}
		else
		{
			while($row1=sqlsrv_fetch_array($result1))
			{
				echo $row1['id_destinazione'];
			}
		}
	}

?>