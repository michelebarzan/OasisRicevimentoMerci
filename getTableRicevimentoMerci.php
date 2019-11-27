<?php

	include "connessione.php";
	include "Session.php";

	set_time_limit(240); 
	
	$ordiniControllatiCompletati=[];
	$ordiniControllatiNonCompletati=[];
	$ordiniNonControllatiNonCompletati=[];

	$query2="SELECT ordine_fornitore, ordine_cliente, stazione, data_registrazione, data_presunta_consegna, controllato, completato, chiuso, nome_fornitore,persona_riferimento,stazione_destinazione,persona_destinazione
			FROM dbo.ricevimento_merci_view WHERE chiuso='false'
			ORDER BY data_registrazione DESC";
	$result2=sqlsrv_query($conn,$query2);
	if($result2==FALSE)
	{
		die('error');
	}
	else
	{
		while($row2=sqlsrv_fetch_array($result2))
		{
			$items=[$row2['ordine_fornitore'],$row2['ordine_cliente'],$row2['nome_fornitore'],$row2['data_registrazione']->format("d/m/Y"),$row2['data_presunta_consegna']->format("d/m/Y"),$row2['stazione_destinazione'],$row2['persona_destinazione']];
						
			if($row2['controllato']=='true' && $row2['completato']=='true')
			{
				array_push($ordiniControllatiCompletati,$items);
			}
			if($row2['controllato']=='true' && $row2['completato']=='false')
			{
				array_push($ordiniControllatiNonCompletati,$items);
			}
			if($row2['controllato']=='false' && $row2['completato']=='false')
			{
				array_push($ordiniNonControllatiNonCompletati,$items);
			}
			/*if($row2['controllato']=='true' && $row2['completato']=='true')
			{
				array_push($ordiniControllatiCompletati,[$row2['ordine_fornitore'],$row2['ordine_cliente'],$row2['nome_fornitore'],$row2['persona_riferimento'],$row2['stazione_destinazione'],$row2['persona_destinazione'],$row2['data_registrazione']->format("d/m/Y"),$row2['data_presunta_consegna']->format("d/m/Y")]);
			}
			if($row2['controllato']=='true' && $row2['completato']=='false')
			{
				array_push($ordiniControllatiNonCompletati,[$row2['ordine_fornitore'],$row2['ordine_cliente'],$row2['nome_fornitore'],$row2['persona_riferimento'],$row2['stazione_destinazione'],$row2['persona_destinazione'],$row2['data_registrazione']->format("d/m/Y"),$row2['data_presunta_consegna']->format("d/m/Y")]);
			}
			if($row2['controllato']=='false' && $row2['completato']=='false')
			{
				array_push($ordiniNonControllatiNonCompletati,[$row2['ordine_fornitore'],$row2['ordine_cliente'],$row2['nome_fornitore'],$row2['persona_riferimento'],$row2['stazione_destinazione'],$row2['persona_destinazione'],$row2['data_registrazione']->format("d/m/Y"),$row2['data_presunta_consegna']->format("d/m/Y")]);
			}*/
		}
		echo '<input type="search" id="RCsearchInputOrdineFornitore" autocomplete="something-new" class="RCsearchInput" placeholder="Ordine f..." onsearch="cleanSearchOrder('.htmlspecialchars(json_encode("RCsearchInputOrdineCliente")).','.htmlspecialchars(json_encode("RCsearchInputNomeCliente")).',this.value,0)" onclick="cleanSearchOrder('.htmlspecialchars(json_encode("RCsearchInputOrdineCliente")).','.htmlspecialchars(json_encode("RCsearchInputNomeCliente")).',this.value,0)" onkeyup="searchOrder(this.value,0);checkEnter(event)" />';
		echo '<input type="search" id="RCsearchInputOrdineCliente" autocomplete="something-new" style="border-left:1px solid #bbb;" class="RCsearchInput" placeholder="Ordine c..." onsearch="cleanSearchOrder('.htmlspecialchars(json_encode("RCsearchInputOrdineFornitore")).','.htmlspecialchars(json_encode("RCsearchInputNomeCliente")).',this.value,1)" onclick="cleanSearchOrder('.htmlspecialchars(json_encode("RCsearchInputOrdineFornitore")).','.htmlspecialchars(json_encode("RCsearchInputNomeCliente")).',this.value,1)" onkeyup="searchOrder(this.value,1);checkEnter(event)" />';
		echo '<input type="search" id="RCsearchInputNomeCliente" autocomplete="something-new" style="border-left:1px solid #bbb;" class="RCsearchInput" placeholder="Nome f..." onsearch="cleanSearchOrder('.htmlspecialchars(json_encode("RCsearchInputOrdineCliente")).','.htmlspecialchars(json_encode("RCsearchInputOrdineFornitore")).',this.value,2)" onclick="cleanSearchOrder('.htmlspecialchars(json_encode("RCsearchInputOrdineCliente")).','.htmlspecialchars(json_encode("RCsearchInputOrdineFornitore")).',this.value,2)" onkeyup="searchOrder(this.value,2);checkEnter(event)" />';
		echo "<div class='RCstatoOrdiniContainer' id='ordiniNonControllatiNonCompletati'>";
			echo '<button class="btnEspandiStatoOrdini" id="btnEspandiStatoOrdini0" onclick="espandiStatoOrdini(this.parentElement.childNodes[1],this.childNodes[1],this.childNodes[2])"><span>Ordini non controllati</span><i class="far fa-chevron-down" id="pinDown0"></i><i style="display:none" class="far fa-chevron-up" id="pinUp0"></i></button>';
			echo "<table class='tableOrdiniRicevimentoMerci' id='tableOrdiniRicevimentoMerciNonControllatiNonCompleti'>";
				echo "<tr>";
					echo "<th class='tableOrdiniRicevimentoMerciCol1'>Ordine f.</th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol2'>Ordine c.</th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol3'>Nome f.<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol4'>D. registrazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol5'>D. consegna<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol6'>S. destinazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol7'>P. destinazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
				echo "</tr>";
				foreach ($ordiniNonControllatiNonCompletati as $row)
				{
					echo "<tr class='tableOrdiniRicevimentoMerciRow' id='rowOrdine".$row[0]."' onclick='getInfoOrdine(".htmlspecialchars(json_encode($row[0])).",".htmlspecialchars(json_encode($row[1])).")'>";
						$k=1;	
						foreach ($row as $cell)
						{
							echo "<td class='tableOrdiniRicevimentoMerciCol$k'>$cell</td>";
							$k++;
						}
					echo "</tr>";
				}
			echo "</table>";
		echo "</div>";
		echo "<div class='RCstatoOrdiniContainer' id='ordiniControllatiNonCompletati'>";
			echo '<button class="btnEspandiStatoOrdini" id="btnEspandiStatoOrdini1" onclick="espandiStatoOrdini(this.parentElement.childNodes[1],this.childNodes[1],this.childNodes[2])"><span>Ordini controllati e incompleti</span><i class="far fa-chevron-down" id="pinDown1"></i><i style="display:none" class="far fa-chevron-up" id="pinUp1"></i></button>';
			echo "<table class='tableOrdiniRicevimentoMerci'  id='tableOrdiniRicevimentoMerciControllatiNonCompleti'>";
				echo "<tr>";
					echo "<th class='tableOrdiniRicevimentoMerciCol1'>Ordine f.</th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol2'>Ordine c.</th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol3'>Nome f.<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol4'>D. registrazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol5'>D. consegna<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol6'>S. destinazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol7'>P. destinazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
				echo "</tr>";
				foreach ($ordiniControllatiNonCompletati as $row)
				{
					echo "<tr class='tableOrdiniRicevimentoMerciRow' id='rowOrdine".$row[0]."' onclick='getInfoOrdine(".htmlspecialchars(json_encode($row[0])).",".htmlspecialchars(json_encode($row[1])).")'>";
						$k=1;
						foreach ($row as $cell)
						{
							echo "<td class='tableOrdiniRicevimentoMerciCol$k'>$cell</td>";
							$k++;
						}
					echo "</tr>";
				}
			echo "</table>";
		echo "</div>";
		echo "<div class='RCstatoOrdiniContainer' id='ordiniControllatiCompletati'>";
			echo '<button class="btnEspandiStatoOrdini" id="btnEspandiStatoOrdini2" onclick="espandiStatoOrdini(this.parentElement.childNodes[1],this.childNodes[1],this.childNodes[2])"><span>Ordini controllati e completi</span><i class="far fa-chevron-down" id="pinDown2"></i><i style="display:none" class="far fa-chevron-up" id="pinUp2"></i></button>';
			echo "<table class='tableOrdiniRicevimentoMerci'  id='tableOrdiniRicevimentoMerciControllatiCompleti'>";
				echo "<tr>";
					echo "<th class='tableOrdiniRicevimentoMerciCol1'>Ordine f.</th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol2'>Ordine c.</th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol3'>Nome f.<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol4'>D. registrazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol5'>D. consegna<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol6'>S. destinazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
					echo "<th class='tableOrdiniRicevimentoMerciCol7'>P. destinazione<i class='fas fa-exchange-alt' title='Mostra altra colonna' style='float:right;margin-right:10px;' onclick='mostraAltraColonna(this.parentElement.className)'></i></th>";
				echo "</tr>";
					foreach ($ordiniControllatiCompletati as $row)
					{
						echo "<tr class='tableOrdiniRicevimentoMerciRow' id='rowOrdine".$row[0]."' onclick='getInfoOrdine(".htmlspecialchars(json_encode($row[0])).",".htmlspecialchars(json_encode($row[1])).")'>";
							$k=1;
							foreach ($row as $cell)
							{
								echo "<td class='tableOrdiniRicevimentoMerciCol$k'>$cell</td>";
								$k++;
							}
						echo "</tr>";
					}
			echo "</table>";
		echo "</div>";
	}

    ?>