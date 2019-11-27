<?php

	include "connessione.php";

	set_time_limit(3000); 
	$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
	$username = 'servizioglobale2019@gmail.com';
	$password = 'Serglo123';
	$imapResource = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' .imap_last_error());

	//If the imap_open function returns a boolean FALSE value,
	//then we failed to connect.
	if($imapResource === false)
	{
		echo "Errore<br>";
		//If it failed, throw an exception that contains
		//the last imap error.
		throw new Exception(imap_last_error());
	}

	//If we get to this point, it means that we have successfully
	//connected to our mailbox via IMAP.

	//Lets get all emails that were received since a given date.
	//$search = 'SINCE "' . date("j F Y", strtotime("-7 days")) . '"';
	
	//$search="ALL";
	
	//echo "ultilma_data_mail: ".$ultilma_data_mail;
		
	$search='SUBJECT ';
	$search=$search.'"Ordine d';
	$search=$search."'acquisto";
	$search=$search.'"';
	//data formato mese/giorno/anno
	if(checkTabellaVuota($conn))
	{
		$ultilma_data_mail=getUltimaDataMail($conn)->format('m/d/Y');
		$search=$search.' SINCE "' . date("j F Y", strtotime($ultilma_data_mail)) . '"';
	}
	//$search=$search.' SINCE "' . date("j F Y", strtotime("03/03/2019")) . '"';
	
	//echo "filtro: ".$search;
	
	$emails = imap_search($imapResource,$search);

	//If the $emails variable is not a boolean FALSE value or
	//an empty array.
	if(!empty($emails))
	{
		//Loop through the emails.
		foreach($emails as $email)
		{
			echo "<br>--------------------------------------------------------------------------------<br>";
			//Fetch an overview of the email.
			$overview = imap_fetch_overview($imapResource, $email);
			$overview = $overview[0];
			//Print out the subject of the email.
			echo '<b>' . htmlentities($overview->subject) . '</b><br>';
			$dataText=htmlentities($overview->date);
			$dataInt=strtotime($dataText);
			$data=date('d/m/Y H:i:s',$dataInt);
			echo $data . '<br>';
			//Print out the sender's email address / from email address.
			echo 'From: ' . $overview->from . '';
			//Get the body of the email.
			$message = imap_fetchbody($imapResource, $email, 1, FT_PEEK);
			echo "<br>--------------------------------------------------------------------------------<br>";
			$oggetto=$overview->subject;
			$ordine_acquisto=str_replace("Ordine d'acquisto "," ",$oggetto);
			$ordine_acquisto=str_replace(' ','',$ordine_acquisto);
			if(strlen($ordine_acquisto)==2)
				$ordine_acquisto='00'.$ordine_acquisto;
			if(strlen($ordine_acquisto)==1)
				$ordine_acquisto='000'.$ordine_acquisto;
			if(strlen($ordine_acquisto)==3)
				$ordine_acquisto='0'.$ordine_acquisto;
			inserisciMail($conn, $overview->to , $overview->from , $overview->subject , '' , $ordine_acquisto , $dataInt );
		}

		$count = 1;

		/* put the newest emails on top */
		rsort($emails);

		/* for every email... */
		foreach($emails as $email_number) 
		{

			/* get information specific to this email */
			$overview = imap_fetch_overview($imapResource,$email_number,0);

			$message = imap_fetchbody($imapResource,$email_number,2);

			//echo "<br><br>".imap_fetchbody($imapResource,$email_number,1.1);

			$overview2 = imap_fetch_overview($imapResource, $email_number);
			$overview2 = $overview[0];

			$subject = htmlentities($overview2->subject);
			
			$dataText=htmlentities($overview2->date);
			$dataInt=strtotime($dataText);
			
			$ordineAcquisto=str_replace("Ordine d'acquisto "," ",$subject);
			$ordineAcquisto=str_replace(' ','',$ordineAcquisto);
			
			if(strlen($ordineAcquisto)==2)
				$ordineAcquisto='00'.$ordineAcquisto;
			if(strlen($ordineAcquisto)==1)
				$ordineAcquisto='000'.$ordineAcquisto;
			if(strlen($ordineAcquisto)==3)
				$ordineAcquisto='0'.$ordineAcquisto;
			
			dropAttachments($conn,$ordineAcquisto);
			
			/* get mail structure */
			$structure = imap_fetchstructure($imapResource, $email_number);

			$attachments = array();

			/* if any attachments found... */
			if(isset($structure->parts) && count($structure->parts)) 
			{
				for($i = 0; $i < count($structure->parts); $i++) 
				{
					$attachments[$i] = array(
						'is_attachment' => false,
						'filename' => '',
						'name' => '',
						'attachment' => ''
					);

					if($structure->parts[$i]->ifdparameters) 
					{
						foreach($structure->parts[$i]->dparameters as $object) 
						{
							if(strtolower($object->attribute) == 'filename') 
							{
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['filename'] = $object->value;
							}
						}
					}

					if($structure->parts[$i]->ifparameters) 
					{
						foreach($structure->parts[$i]->parameters as $object) 
						{
							if(strtolower($object->attribute) == 'name') 
							{
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['name'] = $object->value;
							}
						}
					}

					if($attachments[$i]['is_attachment']) 
					{
						$attachments[$i]['attachment'] = imap_fetchbody($imapResource, $email_number, $i+1);

						/* 3 = BASE64 encoding */
						if($structure->parts[$i]->encoding == 3) 
						{ 
							$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
						}
						/* 4 = QUOTED-PRINTABLE encoding */
						elseif($structure->parts[$i]->encoding == 4) 
						{ 
							$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
						}
					}
				}
			}//echo "guaglio ";
			//if(checkDataMail($conn,$ordineAcquisto,$dataInt))
			//{
				/* iterate through each attachment and save it */
				foreach($attachments as $attachment)
				{
					
					if($attachment['is_attachment'] == 1)
					{
						$filename = $attachment['name'];
						if(empty($filename)) $filename = $attachment['filename'];

						if(empty($filename)) $filename = time() . ".dat";
						$folder = "attachment/".$ordineAcquisto;
						if(!is_dir($folder))
						{
							 mkdir($folder);
						}
						$fp = fopen("./". $folder ."/". $email_number . "-" . $filename, "w+");
						inserisciAllegato($conn,"./". $folder ."/". $email_number . "-" . $filename,$ordineAcquisto);
						fwrite($fp, $attachment['attachment']);
						fclose($fp);
					}
				}
			//}
		}
	} 

	/* close the connection */
	imap_close($imapResource);

	echo "Allegati scaricati";
	
	
	function checkTabellaVuota($conn)
	{
		$query2="SELECT * FROM mail_registrazione_merci";
		$result2=sqlsrv_query($conn,$query2);
		if($result2==FALSE)
		{
			echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));
		}
		else
		{
			return sqlsrv_has_rows( $result2 );
		}
	}
	function dropAttachments($conn,$ordineAcquisto)
	{
		$query2="DELETE FROM [dbo].[allegati_mail_ricezione_merci] WHERE [ordine_acquisto]='$ordineAcquisto'";
		$result2=sqlsrv_query($conn,$query2);
		if(!$result2)
		{
			echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));
		}
		exec('del "C:\\xampp\\htdocs\\OasisRicevimentoMerci\\attachment\\'.$ordineAcquisto.'\\*.*" /Q');
		exec('rmdir "C:\\xampp\\htdocs\\OasisRicevimentoMerci\\attachment\\'.$ordineAcquisto.'"');
	}
	function getUltimaDataMail($conn)
	{
		$query2="SELECT MAX(data_mail) AS ultilma_data_mail FROM mail_registrazione_merci";
		$result2=sqlsrv_query($conn,$query2);
		if($result2==FALSE)
		{
			echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));
		}
		else
		{
			while($row2=sqlsrv_fetch_array($result2))
			{
				return $row2['ultilma_data_mail'];
			}
		}
	}
	function checkDataMail($conn,$ordineAcquisto,$dataInt)
	{
		$data_mail=date('Y-m-d H:i:s',$dataInt);
		$query2="SELECT * FROM mail_registrazione_merci WHERE ordine_acquisto='$ordineAcquisto' AND data_mail<>'$data_mail'";
		//echo $query2;
		$result2=sqlsrv_query($conn,$query2);
		if($result2==FALSE)
		{
			echo "<br><br>Errore esecuzione query<br>Query: ".$query2."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));
		}
		else
		{
			$rows2 = sqlsrv_has_rows( $result2 );
			if ($rows2 === true)
			{
				$query="DELETE allegati_mail_ricezione_merci FROM allegati_mail_ricezione_merci WHERE ordine_acquisto='$ordineAcquisto'";
				$result=sqlsrv_query($conn,$query);
				if(!$result)
				{
					echo "<br><br>Errore esecuzione query<br>Query: ".$query."<br>Errore: ";
					die(print_r(sqlsrv_errors(),TRUE));
				}
				return true;
			}
			else
				return false;
		}
	}
	function inserisciAllegato($conn,$percorso,$ordineAcquisto)
	{
		$query="INSERT INTO [dbo].[allegati_mail_ricezione_merci]
			   ([ordine_acquisto]
			   ,[percorso])
				VALUES ('$ordineAcquisto','$percorso')";
		$result=sqlsrv_query($conn,$query);
		if(!$result)
		{
			echo "<br><br>Errore esecuzione query<br>Query: ".$query."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));
		}
	}
	function inserisciMail($conn, $destinatario , $mittente , $oggetto , $testo , $ordine_acquisto , $data_mail )
	{
		$destinatario=str_replace("'","",$destinatario);
		$mittente=str_replace("'","",$mittente);
		$oggetto=str_replace("'","",$oggetto);
		$testo=str_replace("'","",$testo);
		$destinatario=str_replace('"','',$destinatario);
		$mittente=str_replace('"','',$mittente);
		$oggetto=str_replace('"','',$oggetto);
		$testo=str_replace('"','',$testo);
		
		$data_mail=date('Y-m-d H:i:s',$data_mail);
		
		/*$query="INSERT INTO [dbo].[mail_registrazione_merci]
           ([destinatario]
           ,[mittente]
           ,[oggetto]
           ,[testo]
           ,[ordine_acquisto]
           ,[data_mail]
           ,[data_caricamento])
     VALUES
           ('$destinatario',
           '$mittente',
           '$oggetto',
           '$testo',
           '$ordine_acquisto',
           '$data_mail',
           getDate())";*/
		   $query="INSERT INTO [dbo].[mail_registrazione_merci]
           ([destinatario]
           ,[mittente]
           ,[oggetto]
           ,[testo]
           ,[ordine_acquisto]
           ,[data_mail]
           ,[data_caricamento])
     SELECT '$destinatario',
           '$mittente',
           '$oggetto',
           '$testo',
           '$ordine_acquisto',
           '$data_mail',
           getDate() WHERE $ordine_acquisto NOT IN (SELECT DISTINCT ordine_acquisto FROM mail_registrazione_merci)";
		$result=sqlsrv_query($conn,$query);
		if(!$result)
		{
			echo "<br><br>Errore esecuzione query<br>Query: ".$query."<br>Errore: ";
			die(print_r(sqlsrv_errors(),TRUE));
		}
	}

	
	

    ?>