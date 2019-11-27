<?php
	include "connessione.php";
	include "Session.php";

	set_time_limit(240); 
	
	$ordine_acquisto=$_REQUEST['ordine_acquisto'];

	$query2="SELECT COUNT(*) AS nPdfOrdiniAcquisto,ordine_acquisto FROM [Cecklist].[dbo].[allegati_mail_ricezione_merci] WHERE percorso LIKE '%pdfOrdineAcquisto%' AND ordine_acquisto='$ordine_acquisto' group BY ordine_acquisto";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		die('error');
	}
	else
	{
		while($row2=sqlsrv_fetch_array($result2))
		{
			echo $row2['nPdfOrdiniAcquisto'];
		}
	}
?>