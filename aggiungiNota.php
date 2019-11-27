<?php
	
	include "connessione.php";
	include "Session.php";

	$oggetto=$_REQUEST['oggetto'];
	$testo=$_REQUEST['testo'];
	$ordine_fornitore=$_REQUEST['ordine_fornitore'];
	
	$oggetto=str_replace("'","''",$oggetto);
	$testo=str_replace("'","''",$testo);

	$query2="INSERT INTO [dbo].[note_ricevimento_merci]
           ([testo]
           ,[utente]
           ,[dataOra]
           ,[oggetto]
           ,[ordine_fornitore])
     VALUES
           ('$testo'
           ,".getIdUtente($conn,$_SESSION['Username'])."
           ,GETDATE()
           ,'$oggetto'
           ,'$ordine_fornitore')";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));*/
		die('error');
	}
	else
	{
		echo "ok";
	}

	function getIdUtente($conn,$username)
	{
		$query2="SELECT id_utente FROM utenti WHERE username='$username'";		
		$result2=sqlsrv_query($conn,$query2);
		if($result2==FALSE)
		{
			die("error");
		}
		else
		{
			while($row2=sqlsrv_fetch_array($result2))
			{
				return $row2['id_utente'];
			}
		}
	}

?>