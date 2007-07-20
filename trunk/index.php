<?
#Kommentar, den ich mal testweise einchecke 2007-07-20
session_start();
include("config.php");
$d = opendir("function/inc");
while($f=readdir($d))
     if(str_replace(".","",$f)!="") require_once("function/inc/".$f);

include("function/modules/login.php");
include(userdir."/".$_SESSION["user"]."/settings.php");
if(file_exists(restree)) include(restree);

if($_GET["ajax"]==1) define("ajax","1");

if($_GET["print_restree"]) // This shows the full tree of resources
     die(str_replace("\n","<br />",str_replace(" ","&nbsp;",print_r($GLOBALS["restree"],true))));

if($_GET["sync"]==1)
     include("function/modules/sync.php");

if(ajax==1) {
     switch($_GET["module"]){
          case "contact":
               include("function/modules/detail.contact.php");
          break;
          case "listcontact":
               include("function/modules/list.contact.php");
          break;
          case "todo":
               include("function/modules/detail.todo.php");
          break;
          case "listtodo":
               include("function/modules/list.todo.php");
          break;
          case "calendar":
               include("function/modules/detail.calendar.php");
          break;
          case "listtodo":
               include("function/modules/list.todo.php");
          break;
          case "sync":
               include("function/modules/sync.php");
          break;
     }
} else
     include("function/main.php");

echo $GLOBALS["_OUT"];

?>