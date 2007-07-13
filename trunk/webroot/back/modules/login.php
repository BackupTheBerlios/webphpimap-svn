<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

/* Use this Code-Snippet to avoid Creation of new Accounts
$blocknewaccounts = true;
$d = opendir(userdir);
while($f=readdir($d))
     if(str_replace(".","",$f)!="" && is_dir(userdir."/".$f))
          $valid_users[] = $f;
*/
if(!@in_array($_SESSION["user"],$valid_users)){
     if(singleuser!="" && !singleuserlogin){
          echo "Singleuser has not to login";
          $_SESSION["user"] = singleuser;
     } else {
          if(($_SESSION["user"]=="" && !isset($_POST["user"])) || ($blocknewaccounts && @!in_array($_SESSION["user"],$valid_users))){
               echo "<table border=\"0\" width=\"100%\" height=\"100%\"><tr><td align=\"center\" valign=\"middle\">";
               echo "<form method=\"post\" action=\"\" enctype=\"multipart/form-data\">";
               echo "<input type=\"text\" id=\"user\" name=\"user\"><br />";
               echo "<input type=\"password\" id=\"pw\" name=\"pw\"><br />";
               echo "<input type=\"submit\" value=\"Login\">";
               echo "</form>";
               echo "</table>";
               die();
          }
          /* Use this Code-Snippet to use HTTP-Authentication
          if (!isset($_SERVER['PHP_AUTH_USER']) or !in_array($_SERVER['PHP_AUTH_USER'],$valid_users)) {
               Header("WWW-Authenticate: Basic realm=\"phPIMap\"");
               Header("HTTP/1.0 401 Unauthorized");
               echo "You must login to use phPIMap\n";
               echo $_SERVER["PHP_AUTH_USER"]." --> ".(in_array($_SERVER['PHP_AUTH_USER'],$valid_users)?"in array":"not in array")." --> ".print_r($valid_users,1);
               die();*/
          elseif($_POST["user"]!="") {
               $_SESSION["user"] = strtolower($_POST["user"]); // $_SERVER['PHP_AUTH_USER'];
               $_SESSION["userpw"] = $_POST["pw"];
               $restree = userdir."/".$_SESSION["user"]."/mirror/restree.php";
               if(file_exists($restree)) include($restree);
          }
     }
}

if(!file_exists(userdir."/".$_SESSION["user"])){
     mkdir(userdir."/".$_SESSION["user"],0777);
     mkdir(userdir."/".$_SESSION["user"]."/mirror",0777);
}
if(!file_exists(userdir."/".$_SESSION["user"]."/settings.php"))
     copy("back/settings.def.php",userdir."/".$_SESSION["user"]."/settings.php");

?>
