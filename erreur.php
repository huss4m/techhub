<?php
include('base.php');
include('tete.php');

switch($_GET['erreur'])
{
   case '400':
   avert('Echec de l\'analyse HTTP');
   break;
   case '401':
   avert('Le pseudo et/ou le mot de passe n\'est pas correct !');
   break;
   case '402':
   avert('Le client doit reformuler sa demande avec les bonnes données de paiement.');
   break;
   case '403':
   avert('Requête interdite !');
   break;
   case '404':
   avert('La page n\'existe pas ou n\'existe plus !');
   break;
   case '405':
   avert('Méthode non autorisée');
   break;
   case '500':
   avert('Erreur interne au serveur ou serveur saturé');
   break;
   case '501':
   avert('Le serveur ne supporte pas le service demandé');
   break;
   case '502':
   avert('Mauvaise passerelle');
   break;
   case '503':
   avert(' Service indisponible');
   break;
   case '504':
   avert('Trop de temps à la réponse ');
   break;
   case '505':
   avert('Version HTTP non supportée ');
   break;
   default:
   echo avert('La page n\'existe pas, ou n\'existe plus !');
}

include('pied.php');
?>
