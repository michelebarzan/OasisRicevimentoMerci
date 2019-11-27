<?php

	include "connessione.php";
	include "Session.php";

	set_time_limit(240); 

	$query2="UPDATE registrazioni_ricevimento_merci SET chiuso='true' WHERE chiuso='false' AND ordine_acquisto IN (SELECT DocNum FROM Oasis_Live.dbo.OPOR AS T0 WHERE (DocStatus = 'O'))";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		die('error');
	}
	else
	{
        echo "ok";
    }

    ?>