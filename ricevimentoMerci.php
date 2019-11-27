<?php
	include "Session.php";
	include "connessione.php";

	$pageName="Ricevimento merci";
?>
<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Nunito|Raleway" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Quicksand:300" rel="stylesheet">
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
		<link rel="stylesheet" href="js_libraries/spinners/spinner.css" />
		<script src="js_libraries/spinners/spinner.js"></script>
		<link rel="stylesheet" href="fontawesomepro/css/fontawesomepro.css" />
		<title><?php echo $pageName; ?></title>
		<link rel="stylesheet" href="css/styleV5.css" />
		<script src="struttura.js"></script>
		<script src="js/ricevimentoMerci.js"></script>
		<style>
			.swalContainerMarginTop
			{
				margin-top:60px;
			}
			.swal2-title
			{
				font-family:'Montserrat',sans-serif;
				font-size:18px;
			}
			.swal2-content
			{
				font-family:'Montserrat',sans-serif;
				font-size:13px;
			}
			.swal2-confirm,.swal2-cancel
			{
				font-family:'Montserrat',sans-serif;
				font-size:13px;
			}
			@import url(http://fonts.googleapis.com/css?family=Exo:100,200,400);
			@import url(http://fonts.googleapis.com/css?family=Source+Sans+Pro:700,400,300);
		</style>
	</head>
	<body onload="getTableRicevimentoMerci()">
		<?php include('struttura.php'); ?>
		<div class="RMouterContainer">
			<div class="RMordiniContainer" id="RMordiniContainer"></div>
			<div class="RMtopToolbar">
				<div class="RMtopToolbarTextContainer RMtopToolbarElement">Ordine fornitore: <span id="ordine_fornitore_selected" style="color:#638AB2;font-weight:bold"></span></div>
				<div class="RMtopToolbarTextContainer RMtopToolbarElement">Ordine cliente: <span id="ordine_cliente_selected" style="color:#638AB2;font-weight:bold"></span></div>
				<button class="RMtopToolbarButton RMtopToolbarElement" id="RMtopToolbarButtonRicevuto" >
					<label class="pure-material-checkbox">
						<input type="checkbox" id="RMtopToolbarCheckboxRicevuto" onchange="ordineRicevuto(this.checked)">
						<span>Ordine ricevuto<i class="far fa-archive" style="margin-left:10px"></i></span>
					</label>
				</button>
				<button class="RMtopToolbarButton RMtopToolbarElement" id="RMtopToolbarButtonControllato" style="display:none">
					<label class="pure-material-checkbox">
						<input type="checkbox" id="RMtopToolbarCheckboxControllato" onchange="setStatoOrdine('controllato',this.checked)">
						<span>Ordine controllato<i class="far fa-tasks" style="margin-left:10px"></i></span>
					</label>
				</button>
				<button class="RMtopToolbarButton RMtopToolbarElement" id="RMtopToolbarButtonCompleto" style="display:none">
					<label class="pure-material-checkbox">
						<input type="checkbox" id="RMtopToolbarCheckboxCompletato" onchange="setStatoOrdine('completato',this.checked)">
						<span>Ordine completo<i class="far fa-check" style="margin-left:10px"></i></span>
					</label>
				</button>
				<button class="RMtopToolbarButton RMtopToolbarElement" id="RMtopToolbarButtonDestinazione">
					<label class="pure-material-checkbox">
						<input type="checkbox" id="RMtopToolbarCheckboxDestinazione" onchange="ordineTrasferito(this.checked)">
						<span>Ordine trasferito<i class="far fa-download" style="margin-left:10px"></i></span>
					</label>
				</button>
				<button class="RMtopToolbarButton RMtopToolbarElement" onclick="aggiungiNota()">Aggiungi nota<i class="far fa-comment" style="margin-left:10px"></i></button>
			</div>
			<div class="RMpdfOrdineContainer" id="RMpdfOrdineContainer"></div>
			<div class="RMallegatiOrdineContainer" id="RMallegatiOrdineContainer"></div>
		</div>
		<div id="footer">
			<b>Oasis Group</b>  |  Via Favola 19 33070 San Giovanni PN  |  Tel. +39 0434654752
		</div>
	</body>
</html>
