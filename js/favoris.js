
function add_favoris(id)   // ajouter favoris
{
		
		var xhr;
		
		if (window.XMLHttpRequest) 
		{
			xhr = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject)
		{
				xhr = new ActiveXObject('Microsoft.XMLHTTP');
		} 
		else 
		{
			alert('JavaScript : votre navigateur ne supporte pas les objets XMLHttpRequest...');
			return;
		}
		
		xhr.open('GET','favoris.php?ajouter=' + id + '',true);
		
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4) 
			{
				if (document.getElementById) 
				{
					document.getElementById("forumsL").innerHTML = xhr.responseText;
					document.getElementById("forumsP").innerHTML = "Forums favoris";
				}
			}
		}
		
		 
	xhr.send(null);
}

function delete_favoris(id)  // supprimer favoris
{
		
		var xhr;
		
		if (window.XMLHttpRequest) 
		{
			xhr = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject)
		{
				xhr = new ActiveXObject('Microsoft.XMLHTTP');
		} 
		else 
		{
			alert('JavaScript : votre navigateur ne supporte pas les objets XMLHttpRequest...');
			return;
		}
		
		xhr.open('GET','favoris.php?supprimer=' + id + '',true);
		
		xhr.onreadystatechange = function() 
		{
			if (xhr.readyState == 4) 
			{
				document.getElementById("forumsL").innerHTML = xhr.responseText;
				
			}
		}
		
		
	xhr.send(null);
}
