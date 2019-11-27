<!DOCTYPE HTML>
<html>
	<head>
		<title>Autenticazione</title>
		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
		<link rel="stylesheet" href="css/styleAV2.css" />
		<style>
			@import url(http://fonts.googleapis.com/css?family=Exo:100,200,400);
			@import url(http://fonts.googleapis.com/css?family=Source+Sans+Pro:700,400,300);
		</style>
		<script>
		function login() 
		{
			var username=document.getElementById("username").value;
			var password=document.getElementById("password").value;
			var ricordaPassword=document.getElementById("ricorda").checked;
			//window.alert(username+password+ricordaPassword);
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					if(this.responseText=="ok")
						window.location = 'ricevimentoMerci.php';
					else
					{
						document.getElementById("result").innerHTML = this.responseText;
						document.getElementById("password").style.border="1px solid red";
						document.getElementById("username").style.border="1px solid red";
						document.getElementById("password").value="";
					}
				}
			};
			xmlhttp.open("POST", "autenticazione.php?username=" + username + "&password=" + password + "&ricordaPassword=" + ricordaPassword, true);
			xmlhttp.send();
		}
		window.addEventListener("keydown", windowKeydown, false);
		function windowKeydown(e)
		{
			var keyCode = e.keyCode;
			switch(keyCode) 
			{
				case 13:e.preventDefault();login();break;
			}
		}
	</script>
	</head>
	<body>
		<!--<div id="popup">
			La grafica del sito e' stata aggiornata.<br>Se hai problemi nel visualizzare i contenuti o non vedi alcun cambiamento dalla versione precedente <a href="https://support.google.com/accounts/answer/32050?co=GENIE.Platform%3DDesktop&hl=it" id="linkHistory" target="_blank" >cancella la cronologia dell' ultimo anno</a>, poi chiudi e riapri la pagina.
		</div>-->
		<div id="container" class="container">
			<div id="immagine" class="immagine"></div>
			<div id="accedi" class="accedi">
				<div id="text" class="text">Accedi</div>
				<div  id="input" class="input">
					<form id="autenticazioneF">
						<?php
						if(isset($_COOKIE['username']) && $_COOKIE['username']!="no")
						{
							?>
							<input type="text" value="<?php echo $_COOKIE['username']; ?>" placeholder="Username" name="username" id="username" required><br>
							<?php
						}
						else
						{
							?>
							<input type="text" placeholder="Username" name="username" id="username" required><br>
							<?php
						}
						?>
						<?php
						if(isset($_COOKIE['password']) && $_COOKIE['password']!="no")
						{
							?>
							<input type="password" value="<?php echo $_COOKIE['password']; ?>" placeholder="Password" name="password"  id="password" required><br>
							<?php
						}
						else
						{
							?>
							<input type="password" placeholder="Password" name="password" id="password" required><br>
							<?php
						}
						?>
						<input type="button" value="Login" onclick="login()"><br>
						<div id="result" class="result">&nbsp</div>
						<div id="ricordaPassword" class="ricordaPassword"><input type="checkbox" name="ricordaPassword" id="ricorda" checked>Ricorda password</div>
					</form>
				</div>
				<div id="nuovaPassword" class="nuovaPassword"><a href="cambiaPassword.html" style="color:#666f77; font-weight:bold">Cambia password</a></div><br>
			</div>
		</div>
	</body>
</html>