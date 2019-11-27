<?php
	include "connessione.php";
	
	if($conn)
	{
		$username= $_REQUEST ['username'];
		$P=$_REQUEST ['password'];
		$password=sha1($P);
		$query="SELECT id_utente, username, password FROM utenti";
		$result=sqlsrv_query($conn,$query);
		while($row=sqlsrv_fetch_array($result)) 
		{	
			if($row['username']==$username)
			{
				if($row['password']==$password)
				{
					if($_REQUEST ['ricordaPassword']=='true')
					{
						$hour = time() + 3600 * 24 * 30;
						setcookie('username', $username, $hour);
                        setcookie('password', $P, $hour);
					}
					else
					{
						$hour = time() + 3600 * 24 * 30;
						setcookie('username',"no", $hour);
                        setcookie('password', "no", $hour);
					}
					session_start();
					$_SESSION['Username']=$username;
					$_SESSION['Password']=$password;
					
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
			echo "Username o Password errati";//echo "<script>window.location = 'Autenticazione2.php' </script>";
	}
	else
	{
		echo "Connessione fallita";
		die(print_r(sqlsrv_errors(),TRUE));
	}
	
?>