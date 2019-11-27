<?php

	include "connessione.php";
	include "Session.php";
	
	$destinazione_selected=$_REQUEST['destinazione_selected'];
	$destinazioni=[];
	$destinazione=[];

	$query1="INSERT INTO [dbo].[destinazioni_ricevimento_merci] (destinazione) select [Name] from Oasis_Live.dbo.[@SSAINFO_UBIDOCANA] WHERE [Name] NOT IN (SELECT destinazione FROM [dbo].[destinazioni_ricevimento_merci])";
	$result1=sqlsrv_query($conn,$query1);
	if($result1==FALSE)
	{
		die('error');
	}
	else
	{
		$query2="SELECT * FROM [dbo].[destinazioni_ricevimento_merci] WHERE destinazione<>'$destinazione_selected'";
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
				$destinazioni[$row2['id_destinazione']]=$row2['destinazione']." (".$row2['descrizione'].")";
			}
		}
		echo json_encode($destinazioni)."|";
		$query3="SELECT * FROM [dbo].[destinazioni_ricevimento_merci] WHERE destinazione='$destinazione_selected'";
		$result3=sqlsrv_query($conn,$query3);
		if($result3==FALSE)
		{
			die('error');
		}
		else
		{
			while($row3=sqlsrv_fetch_array($result3))
			{
				$destinazione[$row3['id_destinazione']]=$row3['destinazione']." (".$row3['descrizione'].")";
			}
		}
		echo json_encode($destinazione);
	}

?>