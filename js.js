// Debut - 
function Check_all(container_id,state)
{
	var checkboxes = document.getElementById(container_id).getElementsByTagName('input');
	for (var i=0;i<checkboxes.length;i++)
	{
		if(checkboxes[i].type == 'checkbox')
		{
		checkboxes[i].checked = state;
		}
	}
	return true;
}
  
function ajaxDeclareObjet()
{
	var objet;
	
	if(window.XMLHttpRequest) // FireFox
	{
		objet = new XMLHttpRequest();
	}
	else if(window.ActiveXObject) // Internet Explorer
	{
		try
		{
			objet = new ActiveXObject('Msxml2.XMLHTTP'); // IE 5, 6
		}
		catch(objet)
		{
			objet = new ActiveXObject('Microsoft.XMLHTTP');
		}
	}
	else
	{
		return 'Votre navigateur ne comprend pas la requête envoyée. Veuillez télécharger un navigateur compatible ( Firefox, Opera, Safari, etc ).' ; // S'arrêtera ici
	}
	
	return objet ;
}

function ajaxTraitement(form) // Formulaire, idBloc de l'affichage
{
	var objet = ajaxDeclareObjet(objet);

	if(typeof(objet) == 'string')
	{
		bloc.innerHTML = objet ;
	}


	var i = 0;
	var requete;

	while(form.elements[i])
	{
		elementActuel = form.elements[i] ;
		
		if(elementActuel.type == 'select-multiple') // Si on peut sélectionner plusieurs valeurs pour un seul élément, alors on doit tout lister
		{
			n = 0 ;
			
			while(n < elementActuel.options.length)
			{
				if(elementActuel.options[n].selected == true)
				{
					 requete += '&' + elementActuel.name.replace('[]', '[' + n + ']') + '=' + encodeURIComponent(elementActuel.options[n].value) ;
				}
				
				n++;
			}
		}
		else if((elementActuel.type != 'checkbox' && elementActuel.type != 'radio') || elementActuel.checked == true)// Sinon, si c'est un simple élément ( input, select ), ou que c'est une case à cochée et qu'elle l'est
		{
			requete += '&' + elementActuel.name + '=' + encodeURIComponent(elementActuel.value) ;
		}
		
		i++;
	}

	objet.onreadystatechange = function()
	{
		if(objet.readyState == 4) // Paquet arrivé
		{
			document.getElementById('ajaxChargement').style.visibility = 'hidden' ; // Image de chargement
			
			if(objet.responseText == '' || objet.status == 0) // -> >Rien< n'est renvoyé, c'est probablement une erreur de connexion
			{
				erreur = '\t\tImpossible d\'établir une connexion\n\n' ;
				erreur += 'La communication avec le serveur a échoué. Vérifiez votre connexion internet ainsi que vos paramètres de proxy.' ;
				
				alert(erreur);
			}
			else if(objet.status == 200) // Tout est OK.
			{
				var reponse = objet.responseText ; // On récupère la réponse
				
				if(reponse[0] == '?')
				{
					reponse = reponse.substr(1, reponse.length);
					
					eval(reponse);
				}
				else // S'il n'y a qu'une ligne, c'est un simple message d'erreur, alors on l'affiche simplement
				{
					document.getElementById('ajaxErreur').innerHTML = reponse ;
				}
			}
			else // On a une erreur, on l'affiche
			{
				erreur = '\t\tErreur de type ' + objet.status + '\n\n' ;
				erreur += 'Le serveur a rencontré un problème et ne peut donc pas s\'éxécuter correctement. Voici le détail de l\'erreur :\n' ;
				erreur += objet.statusText ;
				
				alert(erreur);
			}
		}
		else if(objet.readyState >= 0) // Chargement en cours
		{
			document.getElementById('ajaxChargement').style.visibility = 'visible' ;
		}
	}

	action = form.action ;
	objet.open('POST', action, true);  // Methode, url, (a)synchrone ( asynchrone )
	objet.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8'); // Péparation des headers
	objet.send(requete); // Envoie de la requête
	
	vider_area();

	return false;
}

function vider_area()
	{
		bloc = document.getElementById('area');
		bloc.value = '' ;
	}


function getXMLHttpRequest() {
	var xhr = null;

	if (window.XMLHttpRequest || window.ActiveXObject) {
	if (window.ActiveXObject) {
		try {
			xhr = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
	} else {
		xhr = new XMLHttpRequest(); 
	}
	} else {
	alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
	return null;
	}

	return xhr;
	}

	function liste_message() 
	{

	var methode = 'GET';
	var url = 'ajaxChat.php';
	var param = '';
	var cadre = 'listeAjax';

	var XHR = null;

	if(window.XMLHttpRequest) // Firefox
	XHR = new XMLHttpRequest();
	else if(window.ActiveXObject) // Internet Explorer
	XHR = new ActiveXObject("Microsoft.XMLHTTP");
	else 
	{ 
	// XMLHttpRequest non supporté par le navigateur
	alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
	return;
	}



	// envoie de la requête, methode plus url
	XHR.open(methode,url, true);

	// on teste si GET ou POST 
	if(methode == 'POST')
	{
	// si POST envoi du header et des paramètres
	XHR.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	XHR.send(param);
	}
	else
	{
	XHR.send(null);
	}

	// on guette les changements d'état de l'objet
	XHR.onreadystatechange = function attente() {
	// l'état est à 4, requête reçu !
	if(XHR.readyState == 4)     {
	if(XHR.status == 200){
	// ecriture de la réponse
	document.getElementById(cadre).innerHTML = XHR.responseText;
		}
	}
}

// le travail est terminé
return;
}

var isMozilla = (navigator.userAgent.toLowerCase().indexOf('gecko')!=-1) ? true : false;
			var regexp = new RegExp("[\r]","gi");

			function storeCaret(selec)
			{
				if (isMozilla) 
				{
				// Si on est sur Mozilla

					oField = document.forms['form_post'].elements['message'];

					objectValue = oField.value;

					deb = oField.selectionStart;
					fin = oField.selectionEnd;

					objectValueDeb = objectValue.substring( 0 , oField.selectionStart );
					objectValueFin = objectValue.substring( oField.selectionEnd , oField.textLength );
					objectSelected = objectValue.substring( oField.selectionStart ,oField.selectionEnd );

				//	alert("Debut:'"+objectValueDeb+"' ("+deb+")\nFin:'"+objectValueFin+"' ("+fin+")\n\nSelectionné:'"+objectSelected+"'("+(fin-deb)+")");
						
					oField.value = objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]" + objectValueFin;
					oField.selectionStart = strlen(objectValueDeb);
					oField.selectionEnd = strlen(objectValueDeb + "[" + selec + "]" + objectSelected + "[/" + selec + "]");
					oField.focus();
					oField.setSelectionRange(
						objectValueDeb.length + selec.length + 2,
						objectValueDeb.length + selec.length + 2);
				}
				else
				{
				// Si on est sur IE
					
					oField = document.forms['form_post'].elements['message'];
					var str = document.selection.createRange().text;

					if (str.length>0)
					{
					// Si on a selectionné du texte
						var sel = document.selection.createRange();
						sel.text = "[" + selec + "]" + str + "[/" + selec + "]";
						sel.collapse();
						sel.select();
					}
					else
					{
						oField.focus(oField.caretPos);
					//	alert(oField.caretPos+"\n"+oField.value.length+"\n")
						oField.focus(oField.value.length);
						oField.caretPos = document.selection.createRange().duplicate();
						
						var bidon = "%~%";
						var orig = oField.value;
						oField.caretPos.text = bidon;
						var i = oField.value.search(bidon);
						oField.value = orig.substr(0,i) + "[" + selec + "][/" + selec + "]" + orig.substr(i, oField.value.length);
						var r = 0;
						for(n = 0; n < i; n++)
						{if(regexp.test(oField.value.substr(n,2)) == true){r++;}};
						pos = i + 2 + selec.length - r;
						//placer(document.forms['news'].elements['newst'], pos);
						var r = oField.createTextRange();
						r.moveStart('character', pos);
						r.collapse();
						r.select();

					}
				}
			}
			
			function apercu()
{
	
	var formulaire = document.forms['form_post'];
	var formulaireApercu = document.forms['apercuMessage'];
	
	var pseudo = formulaire.elements['pseudo'].value;
	var message = formulaire.elements['message'].value;
	
	if(pseudo == '')
	{
		alert('Vous n\'êtes pas connecté.');
	}
	else if(message == '')
	{
		alert('Vous devez écrire un message.');
	}
	else
	{
		if(!window.focus)
		{
			return false;
		}
		
		formulaireApercu.elements['pseudo'].value = pseudo;
		formulaireApercu.elements['message'].value = message;
		
		window.open('', 'apercuMessage', 'width=700, height=400, scrollbars=yes, resizable=yes');
		formulaireApercu.target = 'apercuMessage' ;
		formulaireApercu.submit();
	}
	
	return false;
}
function add_code(code)
	{
		bloc = document.getElementById('message');
		bloc.value += code;
	}
	function AddText(startTag,defaultText,endTag) 
{
   with(document.form_post)
   {
      if (message.createTextRange) 
      {
         var text;
         message.focus(message.caretPos);
         message.caretPos = document.selection.createRange().duplicate();
         if(message.caretPos.text.length>0)
         {
            //gère les espace de fin de sélection. Un double-click sélectionne le mot
            //+ un espace qu'on ne souhaite pas forcément...
            var sel = message.caretPos.text;
            var fin = '';
            while(sel.substring(sel.length-1, sel.length)==' ')
            {
               sel = sel.substring(0, sel.length-1)
               fin += ' ';
            }
            message.caretPos.text = startTag + sel + endTag + fin;
         }
         else
            message.caretPos.text = startTag+defaultText+endTag;
      }
      else message.value += startTag+defaultText+endTag;
   }
}