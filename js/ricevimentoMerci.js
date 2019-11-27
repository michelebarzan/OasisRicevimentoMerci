    var ordine_fornitore_selected;
    var ordine_cliente_selected;
    var focused;
    var nPdfOrdiniAcquisto=0;
    var nAltriPdf=0;

    function getMails()
    {
        chiudiUserSettings();
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            customClass:
            {
                container: 'swalContainerMarginTop'
            }
        });
        
        var spinnerContainer=document.createElement("div");
        spinnerContainer.setAttribute("class","toastSpinnerContainer");
        
        var spinWrapper=document.createElement("div");
        spinWrapper.setAttribute("class","toast-spin-wrapper");
        
        var spinner=document.createElement("div");
        spinner.setAttribute("class","toastSpinner");
        
        var spinnerLabel=document.createElement("div");
        spinnerLabel.setAttribute("class","toastSpinnerLabel");
        spinnerLabel.innerHTML="Importazione mail in corso...";
        
        spinWrapper.appendChild(spinner);
        
        spinnerContainer.appendChild(spinWrapper);
        spinnerContainer.appendChild(spinnerLabel);
        
        Toast.fire
        ({
            html: spinnerContainer.outerHTML
        })
        $.post("getMails.php",
        function(response, status)
        {
            if(status=="success")
            {
                console.log(response);
                if(response.indexOf("error")>-1)
                {
                    Toast.fire
                    ({
                        type: 'error',
                        html: "<span class='importazioneMailResult'>Errore. Contatta l' amministratore<span>"
                    })
                }
                else
                {
                    var message=response.split("||")[1];
                    Toast.fire
                    ({
                        type: 'success',
                        html: message,
                        timer: 4000
                    })
                    getTableRicevimentoMerci();
                }
            }
            else
                console.log(status);
        });
    }
    async function getTableRicevimentoMerci()
    {
        newCircleSpinner("Caricamento ordini in corso...");

        var responseCheckOrdiniChiusi = await checkOrdiniChiusi();
        if(responseCheckOrdiniChiusi.toLowerCase().indexOf("error")>-1 || responseCheckOrdiniChiusi.toLowerCase().indexOf("notice")>-1 || responseCheckOrdiniChiusi.toLowerCase().indexOf("warning")>-1)
        {
            Swal.fire
            ({
                type: 'warning',
                title: 'Impossibile verificare la chiusura degli ordini',
                text: "Puoi continuare a lavorare, ma potresti trovare alcuni ordini che in realtà sono stati chiusi. Se il problema persiste contatta l' amministratore"
            });
            console.log(responseCheckOrdiniChiusi);
        }

        resetContainers();
        $.post("getTableRicevimentoMerci.php",
        function(response, status)
        {
            if(status=="success")
            {
                //console.log(response);
                document.getElementById("RMordiniContainer").innerHTML=response;
                
                var i=0;
                var table=document.getElementsByClassName("tableOrdiniRicevimentoMerci")[i];
                $(table).show("fast","swing");
                var pinDown=document.getElementById("pinDown"+i);
                var pinUp=document.getElementById("pinUp"+i);
                $(pinDown).hide("fast","swing");
                $(pinUp).show("fast","swing");
                removeCircleSpinner();
            }
            else
                console.log(status);
        });
    }
    function checkOrdiniChiusi()
    {
        return new Promise(function (resolve, reject) 
        {
            $.post("checkOrdiniChiusi.php",
            function(response, status)
            {
                if(status=="success")
                {
                    resolve(response);
                }
                else
                    reject({status});
            });
        });
    }
    function espandiStatoOrdini(table,pinDown,pinUp)
    {
        $(table).toggle("fast","swing");
        $(pinDown).toggle("fast","swing");
        $(pinUp).toggle("fast","swing");
    }
    function resetContainers()
    {
        var all = document.getElementsByClassName("RMtopToolbarElement");
        for (var i = 0; i < all.length; i++) 
        {
            all[i].style.visibility="hidden";
        }
        document.getElementById("RMpdfOrdineContainer").innerHTML="";
        document.getElementById("RMallegatiOrdineContainer").innerHTML="";
        document.getElementById("ordine_fornitore_selected").innerHTML="";
        ordine_fornitore_selected="";
        ordine_cliente_selected="";
        document.getElementById("ordine_cliente_selected").innerHTML="";
    }
    function getInfoOrdine(ordine_fornitore,ordine_cliente)
    {
        focused=null;
        resetContainers();
        var all = document.getElementsByClassName("RMtopToolbarElement");
        for (var i = 0; i < all.length; i++) 
        {
            all[i].style.visibility="visible";
        }
        ordine_fornitore_selected=ordine_fornitore;
        ordine_cliente_selected=ordine_cliente;
        checkButtons(false);
        document.getElementById("ordine_fornitore_selected").innerHTML=ordine_fornitore;
        document.getElementById("ordine_cliente_selected").innerHTML=ordine_cliente;
        var all = document.getElementsByClassName("tableOrdiniRicevimentoMerciRow");
        for (var i = 0; i < all.length; i++) 
        {
            all[i].classList.remove("tableOrdiniRicevimentoMerciRowSelected");
        }
        document.getElementById("rowOrdine"+ordine_fornitore).classList.add("tableOrdiniRicevimentoMerciRowSelected");
        document.getElementById("RMpdfOrdineContainer").innerHTML="";
        document.getElementById("RMallegatiOrdineContainer").innerHTML="";
        var ordine_fornitore_string=ordine_fornitore.toString();
        var ordine_acquisto = ordine_fornitore_string.substr(ordine_fornitore_string.length - 4); // => "Tabs1"
        $.post("getNPdfOrdiniAcquisto.php",
        {
            ordine_acquisto
        },
        function(response, status)
        {
            if(status=="success")
            {
                nPdfOrdiniAcquisto=parseInt(response);
                for (var i = 0; i < nPdfOrdiniAcquisto; i++) 
                {
                    var iframe=document.createElement("iframe");
                    iframe.setAttribute("id","frameOrdineAcquisto"+i);
                    iframe.setAttribute("class","frameOrdineAcquisto");
                    //iframe.setAttribute("onclick","setFocused(this)");
                    //iframe.setAttribute("focusout","focused=null;this.style.border='';");
                    iframe.setAttribute("onload","fixPdf(this)");
                    iframe.setAttribute("src","js_libraries/pdf.js/web/viewer.html?file=attachment/"+ordine_acquisto+"/pdfOrdineAcquisto"+i+".pdf");
                    document.getElementById("RMpdfOrdineContainer").appendChild(iframe);
                }
            }
            else
                console.log(status);
        });
        $.post("getAltriPdf.php",
        {
            ordine_acquisto
        },
        function(response, status)
        {
            if(status=="success")
            {
                var percorsi=[];
                var percorsiObj = JSON.parse(response);
                for (var key in percorsiObj)
                {
                    percorsi.push(percorsiObj[key]);							
                }
                var j=0;
                percorsi.forEach(function(percorso) 
                {
                    var iframe=document.createElement("iframe");
                    iframe.setAttribute("id","frameAltriPdf"+j);
                    iframe.setAttribute("class","frameAltriPdf");
                    //iframe.setAttribute("onclick","setFocused(this)");
                    iframe.setAttribute("onload","fixPdf(this)");
                    iframe.setAttribute("src","js_libraries/pdf.js/web/viewer.html?file="+percorso+"&zoom=200");
                    document.getElementById("RMallegatiOrdineContainer").appendChild(iframe);
                    j++;
                });
                nAltriPdf=j;
            }
            else
                console.log(status);
        });
    }
    function setFocused(iframe)
    {
        focused=iframe;
        //iframe.contentWindow.document.getElementById("cursorHandTool").click();
        var all = document.getElementsByClassName("frameOrdineAcquisto");
        for (var i = 0; i < all.length; i++) 
        {
            all[i].style.border="";
            all[i].contentWindow.document.removeEventListener("keydown", azione);
        }
        var all = document.getElementsByClassName("frameAltriPdf");
        for (var i = 0; i < all.length; i++) 
        {
            all[i].style.border="";
            all[i].contentWindow.document.removeEventListener("keydown", azione);
        }
        
        //opzione 1
        iframe.style.border="3px solid red";
        
        //opzione 2
        /*iframe.style.position="absolute";
        iframe.style.height="100%";
        iframe.style.width="100%";
        iframe.style.left="0";
        iframe.style.right="0";
        iframe.style.top="0";
        iframe.style.bottom="0";*/
        
        //opzione 3
        //iframe.contentWindow.document.getElementById("secondaryPresentationMode").click()
        
        iframe.contentWindow.document.addEventListener("keydown", azione, false);
    }
    function fixPdf(iframe)
    {
        iframe.contentWindow.document.getElementById("sidebarContainer").style.display="none";
        iframe.contentWindow.document.getElementById("scaleSelect").style.display="none";
        iframe.contentWindow.document.getElementById("viewerContainer").style.overflow="hidden";
        //iframe.contentWindow.document.getElementById("cursorHandTool").click();
        
        iframe.contentWindow.document.getElementById("viewerContainer").addEventListener("click", function()
        {
            setFocused(iframe);
        });
    }
    function cleanSearchOrder(idToClean,idToClean2,searchValue,columnIndex)
    {
        console.log(idToClean);
        console.log(idToClean2);
        document.getElementById(idToClean).value="";
        document.getElementById(idToClean2).value="";
        searchOrder(searchValue,columnIndex);
    }
    function searchOrder(searchValue,columnIndex)
    {
        var all = document.getElementsByClassName("tableOrdiniRicevimentoMerci");
        for (var i = 0; i < all.length; i++) 
        {
            table=all[i];
            $(table).show("fast","swing");
            var pinDown=document.getElementById("pinDown"+i);
            var pinUp=document.getElementById("pinUp"+i);
            $(pinDown).hide("fast","swing");
            $(pinUp).show("fast","swing");
        }
        searchValue=searchValue.toLowerCase();
        if(searchValue==null || searchValue=='')
        {
            var all = document.getElementsByClassName("tableOrdiniRicevimentoMerci");
            for (var i = 0; i < all.length; i++) 
            {
                table=all[i];
                for (var j = 0, row; row = table.rows[j]; j++)
                {
                    $(row).show();
                }
            }
        }
        else
        {
            var all = document.getElementsByClassName("tableOrdiniRicevimentoMerci");
            for (var i = 0; i < all.length; i++) 
            {
                table=all[i];
                for (var j = 1, row; row = table.rows[j]; j++)
                {
                    var cellValue=row.cells[columnIndex].innerText.toLowerCase();
                    if(cellValue.indexOf(searchValue)>-1)
                    {
                        $(row).show();
                    }
                    else
                    {
                        $(row).hide();
                    }
                }
            }
        }
    }
    function ordineRicevuto(ricevuto)
    {
        var ordine_fornitore=ordine_fornitore_selected;
        $.post("ordineRicevuto.php",
        {
            ordine_fornitore,
            ricevuto
        },
        function(response, status)
        {
            if(status=="success")
            {
                if(response.indexOf("error")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Se il problema persiste contatta l' amministratore"
                    })
                }
                if(response.indexOf("ok")>-1)
                {
                    checkButtons(true);
                    //console.log("ao");
                    //fixRowsPos(ordine_fornitore);
                }
            }
            else
                console.log(status);
        });
    }
    function checkButtons(frp)
    {
        var ordine_fornitore=ordine_fornitore_selected;
        $.post("checkButtons.php",
        {
            ordine_fornitore
        },
        function(response, status)
        {
            if(status=="success")
            {
                if(response.indexOf("error")>-1)
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Se il problema persiste contatta l' amministratore"
                    })
                }
                else
                {
                    var stati_ordine = JSON.parse(response);
                    if(stati_ordine['ricevuto']=='true')
                        document.getElementById("RMtopToolbarCheckboxRicevuto").checked=true;
                    else
                        document.getElementById("RMtopToolbarCheckboxRicevuto").checked=false;
                    if(stati_ordine['controllato']=='true')
                        document.getElementById("RMtopToolbarCheckboxControllato").checked=true;
                    else
                        document.getElementById("RMtopToolbarCheckboxControllato").checked=false;
                    if(stati_ordine['completato']=='true')
                        document.getElementById("RMtopToolbarCheckboxCompletato").checked=true;
                    else
                        document.getElementById("RMtopToolbarCheckboxCompletato").checked=false;
                    if(stati_ordine['destinazione']=='false')
                    {
                        document.getElementById("RMtopToolbarCheckboxDestinazione").checked=false;
                        document.getElementById("RMtopToolbarButtonDestinazione").removeAttribute("data-tooltip");
                    }
                    else
                    {
                        document.getElementById("RMtopToolbarCheckboxDestinazione").checked=true;
                        document.getElementById("RMtopToolbarButtonDestinazione").setAttribute("data-tooltip","Destinazione: "+stati_ordine['destinazione']);
                    }
                    if(frp)
                        fixRowsPos(ordine_fornitore);
                }
            }
            else
                console.log(status);
        });
    }
    function setStatoOrdine(colonna,valore)
    {
        var error=false;
        if(colonna=='controllato')
        {
            if(document.getElementById("RMtopToolbarCheckboxRicevuto").checked===false)
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Ordine non ancora ricevuto"
                });
                error=true;
            }
        }
        if(colonna=='completato')
        {
            if(document.getElementById("RMtopToolbarCheckboxControllato").checked===false)
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Ordine non ancora controllato"
                });
                error=true;
            }
        }
        if(!error)
        {
            var ordine_fornitore=ordine_fornitore_selected;
            var ordine_cliente=ordine_cliente_selected;
            $.post("setStatoOrdine.php",
            {
                ordine_fornitore,
                colonna,
                valore
            },
            function(response, status)
            {
                if(status=="success")
                {
                    //console.log(response);
                    if(response.indexOf("error")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Se il problema persiste contatta l' amministratore"
                        })
                    }
                    if(response.indexOf("ok")>-1)
                    {
                        checkButtons(true);
                        //fixRowsPos(ordine_fornitore);
                    }
                }
                else
                    console.log(status);
            });
        }
        else
        {
            colonna=colonna.charAt(0).toUpperCase() + colonna.slice(1);
            document.getElementById("RMtopToolbarCheckbox"+colonna).checked=false;
        }
    }
    function fixRowsPos(ordine_fornitore)
    {
        var all = document.getElementsByClassName("tableOrdiniRicevimentoMerci");
        for (var i = 0; i < all.length; i++) 
        {
            table=all[i];
            for (var j = 1, row; row = table.rows[j]; j++)
            {
                if(row.cells[0].innerHTML==ordine_fornitore)
                {
                    var rowToMove=row;
                    var tableFromDelete=table;
                    var indexToRemove=j;
                }
            }
        }
        tableFromDelete.deleteRow(indexToRemove);
        if(document.getElementById("RMtopToolbarCheckboxCompletato").checked==true && document.getElementById("RMtopToolbarCheckboxControllato").checked==true)
        {
            document.getElementById("tableOrdiniRicevimentoMerciControllatiCompleti").appendChild(rowToMove);
            $("#tableOrdiniRicevimentoMerciControllatiCompleti").show("fast","swing");
        }
        if(document.getElementById("RMtopToolbarCheckboxCompletato").checked==false && document.getElementById("RMtopToolbarCheckboxControllato").checked==true)
        {
            document.getElementById("tableOrdiniRicevimentoMerciControllatiNonCompleti").appendChild(rowToMove);
            $("#tableOrdiniRicevimentoMerciControllatiNonCompleti").show("fast","swing");
        }
        if(document.getElementById("RMtopToolbarCheckboxControllato").checked==false)
        {
            document.getElementById("tableOrdiniRicevimentoMerciNonControllatiNonCompleti").appendChild(rowToMove);
            $("#tableOrdiniRicevimentoMerciNonControllatiNonCompleti").show("fast","swing");
        }
    }
    function ordineTrasferito(trasferito)
    {
        if(trasferito===false)
        {
            var ordine_fornitore=ordine_fornitore_selected;
            $.post("ordineTrasferitoFalse.php",
            {
                ordine_fornitore
            },
            function(response, status)
            {
                if(status=="success")
                {
                    if(response.indexOf("error")>-1)
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Se il problema persiste contatta l' amministratore"
                        })
                    }
                    if(response.indexOf("ok")>-1)
                    {
                        checkButtons(true);
                        //fixRowsPos(ordine_fornitore);
                    }
                }
                else
                    console.log(status);
            });
        }
        else
        {
            var error=false;
            if(document.getElementById("RMtopToolbarCheckboxControllato").checked===false)
            {
                Swal.fire
                ({
                    type: 'error',
                    title: 'Errore',
                    text: "Ordine non ancora controllato"
                });
                error=true;
            }
            if(!error)
            {
                var destinazione_selected;
                var all = document.getElementsByClassName("tableOrdiniRicevimentoMerci");
                for (var i = 0; i < all.length; i++) 
                {
                    var table=all[i];
                    for (var j = 0, row; row = table.rows[j]; j++)
                    {
                        //console.log(row.cells[0].innerHTML);
                        if(row.cells[0].innerHTML==ordine_fornitore_selected)
                        {
                            //console.log("waowowao");
                            destinazione_selected=row.cells[5].innerHTML;
                        }
                    }
                }

                var select=document.createElement("select");
                select.setAttribute("id","RMselectDestinazione");
                select.setAttribute("onchange","checkNewDestinazione(this.value)");
                $.post("getDestinazioni.php",
                {
                    destinazione_selected
                },
                function(response, status)
                {
                    if(status=="success")
                    {
                        if(response.indexOf("error")>-1)
                        {
                            Swal.fire
                            ({
                                type: 'error',
                                title: 'Errore',
                                text: "Se il problema persiste contatta l' amministratore"
                            })
                        }
                        else
                        {
                            var destinazioni = JSON.parse(response.split("|")[0]);
                            var destinazione = JSON.parse(response.split("|")[1]);
                            console.log(destinazione);
                            for (var key2 in destinazione) 
                            {
                                if (destinazione.hasOwnProperty(key2)) 
                                {
                                    var option=document.createElement("option");
                                    option.setAttribute("value",key2);
                                    option.innerHTML=destinazione[key2];
                                    select.appendChild(option);
                                }
                            }

                            var destinazioniKeys=[];
                            for (var key in destinazioni) 
                            {
                                if (destinazioni.hasOwnProperty(key)) 
                                {
                                    destinazioniKeys.push(key);
                                }
                            }
                            //destinazioniKeys.reverse();
                            destinazioniKeys.forEach(function(key) 
                            {
                                var option=document.createElement("option");
                                option.setAttribute("value",key);
                                option.innerHTML=destinazioni[key];
                                select.appendChild(option);
                            });
                            var option=document.createElement("option");
                            option.setAttribute("value","new");
                            option.setAttribute("style","color:rgb(var(--pure-material-primary-rgb, 33, 150, 243));");
                            option.innerHTML="Aggiungi destinazione...";
                            select.appendChild(option);
                            Swal.fire
                            ({
                                type: 'question',
                                title: "Scegli la destinazione dell' ordine",
                                html : select.outerHTML
                            }).then((result) => 
                            {
                                if (result.value)
                                {
                                    swal.close();
                                    var destinazione=document.getElementById("RMselectDestinazione").value;
                                    if(destinazione!=null && destinazione!='')
                                    {
                                        var ordine_fornitore=ordine_fornitore_selected;
                                        $.post("setDestinazione.php",
                                        {
                                            ordine_fornitore,
                                            destinazione
                                        },
                                        function(response, status)
                                        {
                                            if(status=="success")
                                            {
                                                if(response.indexOf("error")>-1)
                                                {
                                                    Swal.fire
                                                    ({
                                                        type: 'error',
                                                        title: 'Errore',
                                                        text: "Se il problema persiste contatta l' amministratore"
                                                    })
                                                    document.getElementById("RMtopToolbarCheckboxDestinazione").checked=false;
                                                }
                                                if(response.indexOf("ok")>-1)
                                                {
                                                    checkButtons(true);
                                                    //fixRowsPos(ordine_fornitore);
                                                }
                                            }
                                            else
                                                console.log(status);
                                        });
                                    }
                                }
                                else
                                {
                                    swal.close();
                                    document.getElementById("RMtopToolbarCheckboxDestinazione").checked=false;
                                }
                            })
                        }
                    }
                    else
                        console.log(status);
                });
            }
            else
            {
                document.getElementById("RMtopToolbarCheckboxDestinazione").checked=false;
            }
        }
    }
    function checkNewDestinazione(value)
    {
        if(value=="new")
        {
            document.getElementById("RMtopToolbarCheckboxDestinazione").checked=false;
            
            var destinazione=document.createElement("input");
            destinazione.setAttribute("type","text");
            destinazione.setAttribute("placeholder","Codice destinazione...");
            destinazione.setAttribute("maxlength","50");
            destinazione.setAttribute("class","newDestinazioneInputText");
            destinazione.setAttribute("id","newDestinazioneDestinazione");
            
            var descrizione=document.createElement("textarea");
            descrizione.setAttribute("placeholder","Descrizione destinazione...");
            descrizione.setAttribute("maxlength","250");
            descrizione.setAttribute("class","newDestinazioneTextarea");
            descrizione.setAttribute("id","newDestinazioneDescrizione");
            
            var swalHtml=destinazione.outerHTML+descrizione.outerHTML;
            
            Swal.fire
            ({
                type: 'question',
                title: "Aggiungi una nuova destinazione",
                html : swalHtml
            }).then((result) => 
            {
                if (result.value)
                {
                    swal.close();
                    var destinazione=document.getElementById("newDestinazioneDestinazione").value;
                    var descrizione=document.getElementById("newDestinazioneDescrizione").value;
                    if(destinazione!=null && destinazione!='')
                    {
                        $.post("aggiungiDestinazione.php",
                        {
                            destinazione,
                            descrizione
                        },
                        function(response, status)
                        {
                            if(status=="success")
                            {
                                if(response.indexOf("error")>-1)
                                {
                                    Swal.fire
                                    ({
                                        type: 'error',
                                        title: 'Errore',
                                        text: "Se il problema persiste contatta l' amministratore"
                                    })
                                }
                                else
                                {
                                    document.getElementById("RMtopToolbarCheckboxDestinazione").click();
                                    
                                }
                            }
                            else
                                console.log(status);
                        });
                    }
                    else
                    {
                        Swal.fire
                        ({
                            type: 'error',
                            title: 'Errore',
                            text: "Il codice della destinazione non può essere vuoto"
                        })
                    }
                }
                else
                {
                    swal.close();
                }
            })
        }
    }
    function aggiungiNota()
    {
        var ordine_fornitore=ordine_fornitore_selected;
        
        var oggetto=document.createElement("input");
        oggetto.setAttribute("type","text");
        oggetto.setAttribute("placeholder","Oggetto nota...");
        oggetto.setAttribute("maxlength","230");
        oggetto.setAttribute("class","newDestinazioneInputText");
        oggetto.setAttribute("id","RMoggettoNota");
        
        var testo=document.createElement("textarea");
        testo.setAttribute("placeholder","Testo nota...");
        testo.setAttribute("maxlength","1000");
        testo.setAttribute("class","newDestinazioneTextarea");
        testo.setAttribute("id","RMtestoNota");
        
        var swalHtml=oggetto.outerHTML+testo.outerHTML;
        
        Swal.fire
        ({
            title: "Aggiungi una nota",
            html : swalHtml
        }).then((result) => 
        {
            if (result.value)
            {
                swal.close();
                var oggetto="Nota ordine "+ordine_fornitore+": "+document.getElementById("RMoggettoNota").value;
                var testo=document.getElementById("RMtestoNota").value;
                if(testo!=null && testo!='')
                {
                    $.post("aggiungiNota.php",
                    {
                        oggetto,
                        testo,
                        ordine_fornitore
                    },
                    function(response, status)
                    {
                        if(status=="success")
                        {
                            console.log(response);
                            if(response.indexOf("error")>-1)
                            {
                                Swal.fire
                                ({
                                    type: 'error',
                                    title: 'Errore',
                                    text: "Se il problema persiste contatta l' amministratore"
                                })
                            }
                            else
                            {
                                Swal.fire
                                ({
                                    type: 'success',
                                    title: 'Nota inserita'
                                })
                            }
                        }
                        else
                            console.log(status);
                    });
                }
                else
                {
                    Swal.fire
                    ({
                        type: 'error',
                        title: 'Errore',
                        text: "Il testo della nota non può essere vuoto"
                    })
                }
            }
            else
            {
                swal.close();
            }
        })
    }
    function azione(e) 
    {
        //console.log(e.keyCode);
        e.preventDefault();
        var keyCode = e.keyCode;
        switch(keyCode) 
        {
            case 37:scrollleft();break;
            case 39:scrollright();break;
            case 38:scrolltop();break;
            case 40:scrolldown();break;
            case 107:zoomin();break;
            case 187:zoomin();break;
            case 109:zoomout();break;
            case 189:zoomout();break;
            case 70:focused.contentWindow.document.getElementById("secondaryPresentationMode").click();break;
            case 9:moveFocus();break;
            case 27:focused.style.border="";focused=null;break;
            //case 49:if(document.activeElement.id!='RCsearchInputOrdineCliente'){document.getElementById("RCsearchInputOrdineFornitore").click();document.getElementById("RCsearchInputOrdineFornitore").focus();}break;
        }
    }
    function zoomin()
    {
        var iframe = focused;
        var elmnt = iframe.contentWindow.document.getElementById("zoomIn").click();
    }
    function zoomout()
    {
        var iframe = focused;
        var elmnt = iframe.contentWindow.document.getElementById("zoomOut").click();
    }
    function scrolltop()
    {
        var iframe = focused;
        iframe.contentWindow.document.getElementById("viewerContainer").scrollTop-=50;
    }
    function scrolldown()
    {
        var iframe = focused;
        iframe.contentWindow.document.getElementById("viewerContainer").scrollTop+=50;
    }
    function scrollleft()
    {
        var iframe = focused;
        iframe.contentWindow.document.getElementById("viewerContainer").scrollLeft-=50;
    }
    function scrollright()
    {
        var iframe = focused;
        iframe.contentWindow.document.getElementById("viewerContainer").scrollLeft+=50;
    }
    window.addEventListener("keydown", windowKeydown, false);
    function windowKeydown(e)
    {
        var keyCode = e.keyCode;
        switch(keyCode) 
        {
            case 9:e.preventDefault();moveFocus();break;
            case 27:e.preventDefault();escapeSelected();break;
            //case 49:if(document.activeElement.id!='RCsearchInputOrdineCliente'){document.getElementById("RCsearchInputOrdineFornitore").click();document.getElementById("RCsearchInputOrdineFornitore").focus();}break;
        }
    }
    function escapeSelected()
    {
        if(ordine_fornitore_selected!=null && (nAltriPdf+nPdfOrdiniAcquisto)>0)
        {
            //opzione 1
            focused.style.border="";
            focused=null;
        }
    }
    function moveFocus()
    {
        if(ordine_fornitore_selected!=null && (nAltriPdf+nPdfOrdiniAcquisto)>0)
        {
            if(focused==null || focused=='')
            {
                var nextFrame=document.getElementById("frameOrdineAcquisto0");
                setFocused(nextFrame);
                nextFrame.contentWindow.document.getElementById("viewerContainer").focus();
            }
            else
            {
                if(focused.id.indexOf("frameAltriPdf")>-1 && focused.id.replace("frameAltriPdf","")==nAltriPdf-1)
                {
                    focused=null;
                    moveFocus();
                }
                else
                {
                    var nextFrameId=$(focused).next('iframe').attr('id');
                    var nextFrame=document.getElementById(nextFrameId);
                    try
                    {
                        setFocused(nextFrame);
                        nextFrame.contentWindow.document.getElementById("viewerContainer").focus();
                    }
                    catch(err)
                    {
                        try
                        {
                            var nextFrame=document.getElementById("frameAltriPdf0");
                            setFocused(nextFrame);
                            nextFrame.contentWindow.document.getElementById("viewerContainer").focus();
                        }
                        catch(err){}
                    }
                }
            }
        }
    }
    function checkEnter(e)
    {
        var keyCode = e.keyCode;
        switch(keyCode) 
        {
            case 13:checkFilteredRows();break;
        }
    }
    function checkFilteredRows()
    {
        var numRows=0;
        var visibleRows=[];
        var all = document.getElementsByClassName("tableOrdiniRicevimentoMerci");
        for (var i = 0; i < all.length; i++) 
        {
            var table=all[i];
            for (var j = 1, row; row = table.rows[j]; j++)
            {
                if($(row).is(":visible"))
                {
                    numRows++;
                    visibleRows.push(row);
                }
            }
        }
        if(numRows==1)
        {
            var myRow=visibleRows[0];
            getInfoOrdine(myRow.cells[0].innerHTML,myRow.cells[1].innerHTML);
        }
    }
    function mostraAltraColonna(classToHide)
    {
        $("."+classToHide).hide();
        if(classToHide=="tableOrdiniRicevimentoMerciCol7")
        {
            $(".tableOrdiniRicevimentoMerciCol3").show();
            $(".tableOrdiniRicevimentoMerciCol4").show();
            $(".tableOrdiniRicevimentoMerciCol5").show();
            $(".tableOrdiniRicevimentoMerciCol6").show();
            $(".tableOrdiniRicevimentoMerciCol7").show();
        }
    }