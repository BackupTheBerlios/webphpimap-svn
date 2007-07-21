<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

$_SESSION["norefresh"] = false; // Prevents from refreshing IMAP-Resource if set to yes

define("domain",getenv("SERVER_STRING"));
define("designroot",".");
define("phproot","function");
define("modulesroot",phproot."/modules");
define("verboseMode",false);
define("verboseHead",false);
define("phPIMap","ok");
define("secureDelimiter","*#####*");
define("userdir","users");

if($_GET["ajax"]==1) define("ajax",true);
else define("ajax",false);

// If this value is set (!=""), then just this user is allowed
define("singleuser","");
// If this value is true, the singleuser also must login, otherwise his password is not prompted and must be stored in the user-config-file
define("singleuserlogin",false);

/* Resources are saved in an array
          $restree[$type][$i][$field] = $value;

   Possible values are as follows in $fields[$type]
 */
define("internalid","phPIMap_id");
$fields["todo"] = Array("pimfile",internalid,"uid","summary","due");
$fields["calendar"] = Array("pimfile",internalid,"uid","summary","from","to");
$fields["contact"] = Array("pimfile",internalid,"uid","surname","firstname","street","zip","city","country","telephone","mobilephone","cellphone","fax","email","birthday","url","title","organization","note");


define("mirror",userdir."/".$_SESSION["user"]."/mirror");
define("restree",mirror."/restree.php");

$types = Array("todo","calendar","contact");

// List all Modules
define("defaultmodule","calendar");

$GLOBALS["_MODULES"]["calendar"][0] = "Calendar";
$GLOBALS["_MODULES"]["calendar"][1] = modulesroot."/mod.calendar.php";

$GLOBALS["_MODULES"]["contact"][0] = "Contact";
$GLOBALS["_MODULES"]["contact"][1] = modulesroot."/mod.contact.php";

$GLOBALS["_MODULES"]["todo"][0] = "Todo";
$GLOBALS["_MODULES"]["todo"][1] = modulesroot."/mod.todo.php";

$GLOBALS["_MODULES"]["login"][0] = "Login";
$GLOBALS["_MODULES"]["login"][1] = modulesroot."/mod.login.php";

$GLOBALS["_MODULES"]["sync"][0] = "Sync";
$GLOBALS["_MODULES"]["sync"][1] = modulesroot."/mod.sync.php";

?>