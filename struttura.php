	<div id="header" class="header" >
		<div id="pageName" class="pageName"><?php echo $pageName; ?></div>
		<div id="user" class="user">
			<div id="username"><?php echo $_SESSION['Username']; ?></div>
			<input type="button" value="" id="btnUser">
			<input type="button" value="" onclick="apriNotifiche()" id="btnNotifica">
			<input type="button" id="btnNuovaNotifica" value="" >
			<div id="notifichePadding"></div>
			<div id="notifiche">
				<script>
					document.getElementById("user").addEventListener("mouseover", function()
					{
						if(document.getElementById('notifiche').style.display=="inline-block")
							apriNotifiche();	
						if(document.getElementById('notifichePadding').style.display=="inline-block")
							apriNotifiche();	
					});
				
					document.getElementById("notifiche").addEventListener("mouseover", function()
					{
						apriNotifiche();							
					});
					document.getElementById("notifiche").addEventListener("mouseout", function()
					{
						chiudiNotifiche();							
					});
					document.getElementById("notifichePadding").addEventListener("mouseover", function()
					{
						apriNotifiche();							
					});
				</script>
				<div id="userSettingsRow1">
					<div id="titoloUserSettings">Notifiche</div>
					<input type="button" value="" id="btnChiudiUserSettings" onclick="chiudiNotifiche()">
				</div>
				<div id="containerNotifiche">
					<div id="nessunaNotifica">
						Nessuna notifica
					</div>
				</div>
			</div>
			
			<input type="button" value="" onclick="apriUserSettings()" id="btnUserSettings">
			<div id="userSettingsPadding"></div>
			<div id="userSettings">
				<script>
					document.getElementById("user").addEventListener("mouseover", function() 
					{
						if(document.getElementById('userSettings').style.display=="inline-block")
							apriUserSettings();	
						if(document.getElementById('userSettingsPadding').style.display=="inline-block")
							apriUserSettings();	
					});
				
					document.getElementById("userSettings").addEventListener("mouseover", function()
					{
						apriUserSettings();							
					});
					document.getElementById("userSettings").addEventListener("mouseout", function()
					{
						chiudiUserSettings();							
					});
					document.getElementById("userSettingsPadding").addEventListener("mouseover", function()
					{
						apriUserSettings();							
					});
					setInterval(function()
					{
						if(document.getElementById('btnUserSettings').offsetWidth!="24")
						{
							chiudiUserSettings();
							chiudiNotifiche();	
						}
					}, 100);
				</script>
				<div id="userSettingsRow1">
					<div id="titoloUserSettings">Impostazioni</div>
					<input type="button" value="" id="btnChiudiUserSettings" onclick="chiudiUserSettings()">
				</div>
				<div id="userSettingsRow2">
					<?php getNomeCognome($conn,$_SESSION['Username']); ?>
				</div>
				<div id="userSettingsRow2">
					<a id="userSettingsCambiaPassword" href="cambiaPassword.html">Cambia password</a>
				</div>
				<div id="userSettingsRow2">
					<button class="userSettingsImportaMail" onclick="getMails()">Importa mail<i class="fal fa-download" style="margin-left:15px;"></i></button>
				</div>
				<div id="permessiUserSettings">
					<div id="userSettingsRow3">
						Hai accesso alle pagine:
					</div>
					<?php getPermessi($conn,$_SESSION['Username']); ?>
				</div>
			</div>
			<input type="button" value="Logout" id="btnLogout" onclick="logoutB()">
		</div>
	</div>
	
	<?php 
		function getNomeCognome($conn,$username) 
		{
			$q="SELECT nome,cognome FROM utenti WHERE username='$username'";
			$r=sqlsrv_query($conn,$q);
			if($r==FALSE)
			{
				echo "<br><br>Errore esecuzione query<br>Query: ".$q."<br>Errore: ";
				die(print_r(sqlsrv_errors(),TRUE));
			}
			else
			{
				while($row=sqlsrv_fetch_array($r))
				{
					echo $row['nome']." ".$row['cognome'];
				}
			}
		}
		
		function getPermessi($conn,$username) 
		{
			echo "Ricevimento merci<br>";
			/*$q="SELECT DISTINCT descrizione,pagina FROM accesso_pagine";
			$r=sqlsrv_query($conn,$q);
			if($r==FALSE)
			{
				echo "<br><br>Errore esecuzione query<br>Query: ".$q."<br>Errore: ";
				die(print_r(sqlsrv_errors(),TRUE));
			}
			else
			{
				while($row=sqlsrv_fetch_array($r))
				{
					$q2="SELECT accesso_pagine.accesso FROM accesso_pagine,utenti WHERE accesso_pagine.utente=utenti.id_utente AND utenti.username='$username' AND accesso_pagine.pagina='".$row['pagina']."'";
					$r2=sqlsrv_query($conn,$q2);
					if($r2==FALSE)
					{
						echo "<br><br>Errore esecuzione query<br>Query: ".$q2."<br>Errore: ";
						die(print_r(sqlsrv_errors(),TRUE));
					}
					else
					{
						$rows = sqlsrv_has_rows( $r2 );  
						if ($rows === true)  
						{						
							while($row2=sqlsrv_fetch_array($r2))
							{
								if($row2['accesso']!='negato')
									echo $row['descrizione']."<br>";
							}
						}
						else
							echo $row['descrizione']."<br>";
					}
				}
			}*/
		}
	?>