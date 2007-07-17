<?
#Kommentar, den ich mal testweise einchecke
session_start();
include("config.php");
$d = opendir("back/inc");
while($f=readdir($d))
     if(str_replace(".","",$f)!="") require_once("back/inc/".$f);

include("back/modules/login.php");
include(userdir."/".$_SESSION["user"]."/settings.php");
if(file_exists(restree)) include(restree);

if($_GET["ajax"]==1) define("ajax","1");

if($_GET["print_restree"]) // This shows the full tree of resources
     die(str_replace("\n","<br />",str_replace(" ","&nbsp;",print_r($GLOBALS["restree"],true))));

if($_GET["sync"]==1)
     include("back/modules/sync.php");

if(ajax==1) {
     switch($_GET["module"]){
          case "contact":
               include("back/modules/detail.contact.php");
          break;
          case "listcontact":
               include("back/modules/list.contact.php");
          break;
          case "todo":
               include("back/modules/detail.todo.php");
          break;
          case "listtodo":
               include("back/modules/list.todo.php");
          break;
          case "calendar":
               include("back/modules/detail.calendar.php");
          break;
          case "listtodo":
               include("back/modules/list.todo.php");
          break;
          case "sync":
               include("back/modules/sync.php");
          break;
     }
} else
     include("back/main.php");

echo $GLOBALS["_OUT"];

?>