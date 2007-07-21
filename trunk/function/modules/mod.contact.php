<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

switch($State){
     case "app":
          include("list.contact.php");
     break;
     default:
          include("detail.contact.php");

}


?>