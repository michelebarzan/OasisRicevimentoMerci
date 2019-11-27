<?php
	include "connessione.php";
	
	if($conn)
	{
		$Username= $_REQUEST ['username'];
		$P=$_REQUEST ['passwordVecchia'];
		$nuovaPassword1=$_REQUEST ['nuovaPassword1'];
		$nuovaPassword2=$_REQUEST ['nuovaPassword2'];
		$passwordVecchia=sha1($P);
		
		$query="SELECT username, password FROM utenti";
		$result=sqlsrv_query($conn,$query);
		while($row=sqlsrv_fetch_array($result)) 
		{	
			if($row['username']==$Username)
			{
				if($row['password']==$passwordVecchia)
				{
					$p1="'".sha1($nuovaPassword1)."'";
					$q="UPDATE utenti SET password = $p1 WHERE username = '$Username'";
					$r=sqlsrv_query($conn,$q);
					if($r==FALSE)
					{
						echo "<br><br>Errore esecuzione query<br>Query: ".$q."<br>Errore: ";
						die(print_r(sqlsrv_errors(),TRUE));
					}
					echo "ok";
					$errore="No";
					break;
				}
				else
				{
					$errore="Si";
				}
			}
			else
			{
				$errore="Si";
			}
		}
		if($errore=="Si")
		{
			echo "Username o password errati";
		}
	}
	else
	{
		echo "Connessione fallita";
		die(print_r(sqlsrv_errors(),TRUE));
	}
	
?>