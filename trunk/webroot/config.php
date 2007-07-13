<?

$_SESSION["norefresh"] = false; // Prevents from refreshing IMAP-Resource if set to yes

define("verboseMode",false);
define("verboseHead",true);
define("phPIMap","ok");
define("secureDelimiter","*#####*");
define("userdir","../users");

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


?>