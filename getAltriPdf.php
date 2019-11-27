<?php
	include "connessione.php";
	include "Session.php";

	set_time_limit(240); 
	
	$ordine_acquisto=$_REQUEST['ordine_acquisto'];

	$percorsi=[];

	$query2="SELECT REPLACE(percorso, './js_libraries/pdf.js/web/', '') AS percorso, ordine_acquisto
			FROM dbo.allegati_mail_ricezione_merci
			WHERE (percorso NOT LIKE '%pdfOrdineAcquisto%') AND (ordine_acquisto = '$ordine_acquisto')";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		die('error');
	}
	else
	{
		while($row2=sqlsrv_fetch_array($result2))
		{
			array_push($percorsi,$row2['percorso']);
			//echo $row2['percorso'];
		}
		echo json_encode($percorsi);
	}
?>