<?php
include('base.php');
include('tete.php');
$faq = file_get_contents('txt/faq.txt');
echo'<div class="bloc2">
<h3>La FAQ du site: </h3>
<div class="texte">
'.$faq.'
</div>
</div>
';
include('pied.php');
?> 
