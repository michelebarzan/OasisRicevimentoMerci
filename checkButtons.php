<?php

	include "connessione.php";
	include "Session.php";
	
	$ordine_fornitore=$_REQUEST['ordine_fornitore'];
	$stati_ordine=[];
	
	$stati_ordine['controllato']='false';
	$stati_ordine['completato']='false';
	$stati_ordine['ricevuto']='false';
	$stati_ordine['destinazione']='false';
	
	$query2="SELECT * FROM [dbo].[registrazioni_ricevimento_merci] WHERE ordine_acquisto='$ordine_fornitore'";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));*/
		die('error');
	}
	else
	{
		while($row2=sqlsrv_fetch_array($result2))
		{
			$stati_ordine['controllato']=$row2['controllato'];
			$stati_ordine['completato']=$row2['completato'];
			$stati_ordine['ricevuto']=$row2['ricevuto'];
			if($row2['destinazione']==NULL || $row2['destinazione']=='NULL')
				$stati_ordine['destinazione']='false';
			else
			{
				$stati_ordine['destinazione']=getDestinazione($conn,$row2['destinazione']);
			}
		}
	}
	echo json_encode($stati_ordine);
	
	function getDestinazione($conn,$id_destinazione)
	{
		$query2="SELECT * FROM [destinazioni_ricevimento_merci] WHERE id_destinazione=$id_destinazione";		
		$result2=sqlsrv_query($conn,$query2);
		if($result2==FALSE)
		{
			die("error");
		}
		else
		{
			while($row2=sqlsrv_fetch_array($result2))
			{
				return $row2['destinazione']." (".$row2['descrizione'].")";
			}
		}
	}
	
?>