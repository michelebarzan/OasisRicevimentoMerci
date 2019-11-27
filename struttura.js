	function topFunction() 
	{
		document.body.scrollTop = 0;
		document.documentElement.scrollTop = 0;
	}
	function apri()
	{
		topFunction();
		var body = document.body,html = document.documentElement;
		var offsetHeight = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
		document.getElementById('stato').value="Aperto";
		document.getElementById('navBar').style.width="300px";
		document.getElementById('nascondi2').style.display="inline-block";
		document.getElementById('nascondi2').value="ME";
		document.getElementById('nascondi3').style.display="inline-block";
		document.getElementById('nascondi3').value="NU";
		document.getElementById('navBar').style.height = offsetHeight+"px";
		var all = document.getElementsByClassName("btnGoToPath");
		for (var i = 0; i < all.length; i++) 
		{
		all[i].style.width = '100%';
		all[i].style.height='50px';
		all[i].style.borderBottom='1px solid #ddd';
		}
	}
	function chiudi()
	{
		document.getElementById('navBar').style.width = "0px";
		document.getElementById('stato').value="Chiuso";
		document.getElementById('nascondi2').value="";
		document.getElementById('nascondi3').value="";
		setTimeout(function()
		{ 
		document.getElementById('navBar').style.height = "0px";
		document.getElementById('nascondi2').style.display="none";
		document.getElementById('nascondi3').style.display="none";
		var all = document.getElementsByClassName("btnGoToPath");
		for (var i = 0; i < all.length; i++) 
		{
		all[i].style.width = '0px';
		all[i].style.height='0px';
		all[i].style.borderBottom='';
		}
		}, 1000);
	}
	function logoutB()
	{
		window.location = 'logout.php';
	}
	function gotopath(path)
	{
		window.location = path;
	}
	function homepage()
	{
		window.location = 'index.php';
	}
	function nascondi()
	{
		var stato=document.getElementById('stato').value;
		if(stato=="Aperto")
		{
		chiudi();
		}
		else
		{
		apri();
		}
	}
	function goToPath(path)
	{
		window.location = path;
	}
	function apriUserSettings()
	{
		document.getElementById("userSettings").style.display="inline-block";
		document.getElementById("userSettingsPadding").style.display="inline-block";
		chiudiNotifiche();
	}
	function chiudiUserSettings()
	{
		document.getElementById("userSettings").style.display="none";
		document.getElementById("userSettingsPadding").style.display="none";
	}
	function apriNotifiche()
	{
		document.getElementById("notifiche").style.display="inline-block";
		document.getElementById("notifichePadding").style.display="inline-block";
		notificaVista();
		chiudiUserSettings();
	}
	function chiudiNotifiche()
	{
		document.getElementById("notifiche").style.display="none";
		document.getElementById("notifichePadding").style.display="none";
	}
	function eliminaNotifiche()
	{
		document.getElementById('containerNotifiche').innerHTML="";
	}
	function aggiungiNotifica(testo)
	{
		document.getElementById('containerNotifiche').innerHTML+="<div class='notificheRow'>"+testo+"</div>";
		document.getElementById("btnNuovaNotifica").style.visibility = "visible";
	}
	function notificaVista()
	{
		document.getElementById("btnNuovaNotifica").style.visibility = "hidden";
	}