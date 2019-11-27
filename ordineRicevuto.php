<?php

	include "connessione.php";
	include "Session.php";
	
	$ordine_fornitore=$_REQUEST['ordine_fornitore'];
	$ricevuto=$_REQUEST['ricevuto'];
	
	$query1="SELECT * FROM [dbo].[registrazioni_ricevimento_merci] WHERE [ordine_acquisto] = '$ordine_fornitore'";
	$result1=sqlsrv_query($conn,$query1);
	if($result1==FALSE)
	{
		/*echo "<br><br>Errore esecuzione query<br>Query: ".$query1."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));*/
		die('error');
	}
	else
	{
		$rows = sqlsrv_has_rows( $result1 );
		if ($rows === true)
		{
			$query2="UPDATE [dbo].[registrazioni_ricevimento_merci] SET [ricevuto]='$ricevuto',dataOra=GETDATE() WHERE [ordine_acquisto] = '$ordine_fornitore'";
			$result2=sqlsrv_query($conn,$query2);
			if($result2==FALSE)
			{
				/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
					die(print_r(sqlsrv_errors(),TRUE));*/
				die('error');
			}
			else
			{
				if($ricevuto=='false')
				{
					$query3="UPDATE [dbo].[registrazioni_ricevimento_merci] SET [controllato]='false',[completato]='false',[destinazione]=NULL,dataOra=NULL WHERE [ordine_acquisto] = '$ordine_fornitore'";
					$result3=sqlsrv_query($conn,$query3);
					if($result3==FALSE)
					{
						/*echo "<br><br>Errore esecuzione query<br>Query: ".$query3."<br>Errore: ";
							die(print_r(sqlsrv_errors(),TRUE));*/
						die('error');
					}
					else
					{
						echo "ok";
					}
				}
			}
		}
		else 
		{
			$query2="INSERT INTO [dbo].[registrazioni_ricevimento_merci]
           ([dataOra]
           ,[utente]
           ,[ordine_acquisto]
           ,[destinazione]
           ,[controllato]
           ,[completato]
           ,[chiuso]
           ,[ricevuto])
		   VALUES
		   (GETDATE()
           ,".getIdUtente($conn,$_SESSION['Username'])."
           ,'$ordine_fornitore'
           ,NULL
           ,'false'
           ,'false'
           ,'false'
           ,'true')";
			$result2=sqlsrv_query($conn,$query2);
			if($result2==FALSE)
			{
				/*echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
					die(print_r(sqlsrv_errors(),TRUE));*/
				die('error');
			}
			else
				echo "ok";
		}
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