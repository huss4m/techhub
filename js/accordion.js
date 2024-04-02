/******************************************
# Auteur : Julien Theler - www.twiip.ch
# Contact : julien.theler@twiip.ch
# Licence : CC-by-nc
******************************************/

function accordion(div, elementheight){
	//Recherche de toutes les divs concern�s
	var divs = document.getElementsByTagName('div');
	for(var i=0; i<divs.length; i++){
		if(divs[i].className == 'accordion'){ //On parcourt toutes les divs ayant la class "accordion"
			var p = divs[i].getElementsByTagName('p')[0];
			
			if(divs[i] == div){
				if(p.offsetHeight != 0){
					//On referme le paragraphe
					var height = 0;
				}
				else{
					//On r�cup�re la hauteur du paragraphe
					p.style.display = 'block';
					p.style.height = '';
					var height = p.offsetHeight;
					p.style.height = '0px';
				}
				//On lance l'ouverture du paragraphe
				accordionLoop(i, height);
			}
			else if(p.offsetHeight != 0){
				//On lance la boucle pour masquer l'�l�ment
				accordionLoop(i, 0);
			}
		}
	}
}

function accordionLoop(div, targetHeight){
	var div_element = document.getElementsByTagName('div')[div]; //Div concern�e
	var p = div_element.getElementsByTagName('p')[0]; //Paragraphe � ouvrir/fermer
	
	var height = parseInt(p.style.height.replace(/px/, ''));
	var sens = (height < targetHeight ? 1 : -1); //On compare la taille actuelle � la taille � atteindre pour savoir si on ouvre ou ferme un paragraphe
	height = height+(sens*30); //On fait varier la hauteur de 20px � chaque boucle (-20 pour refermer, +20 pour ouvrir)
	
	if((sens == 1 && height > targetHeight) || sens == -1 && height < targetHeight){ //Pour ne pas d�passer la taille du paragraphe
		height = targetHeight;
	}
	p.style.height = height+'px';
	
	if(height != targetHeight){
		setTimeout('accordionLoop('+div+', '+targetHeight+')', 20);
	}
	else if(targetHeight == 0 && sens == -1){
		//On masque le paragraphe referm�
		p.style.display = 'none';
	}
}