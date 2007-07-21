<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

switch($State){
     case "app":
          include("list.todo.php");
     break;
     default:
          include("detail.todo.php");

}


?>