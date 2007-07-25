<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

session_start();
# Read necessary configuration
include("config.php");
# Include Files of phproot."/inc"
$d = opendir(phproot."/inc");
while($f=readdir($d))
     if(str_replace(".","",$f)!="" && $f!=".svn") require_once(phproot."/inc/".$f);

# Checks whether the User is logged in and prevents further operations if he/she is not
include("function/modules/mod.login.php");
# Otherwise user-specific settings are loaded
include(userdir."/".$_SESSION["user"]."/settings.php");
# If User already has downloaded his/her PIM-Resources, they are stored in a file
# in the User-Dir referenced by the constant "restree"
if(file_exists(restree)) include(restree);

# This code-snippet offers the possibility to view the full tree of resources
# for development purposes
if($_GET["print_restree"])
     die(str_replace("\n","<br />",str_replace(" ","&nbsp;",print_r($GLOBALS["restree"],true))));

# The called module should be given by POST or GET
# If no module is given, the standard module is taken (see config.php)
define("inc_main",((RetrieveVar("module","0110")!="")?RetrieveVar("module","0110"):defaultmodule));

# If this is an ajax-call just start the mainpage without embracing index.html
if(ajax) $GLOBALS["stdOUT"] = "~mainpage~".inc_main."~/mainpage~";
else $GLOBALS["stdOUT"] = file_get_contents(designroot."/display/index.html");

# Analyse Output for template-Commands
$GLOBALS["stdOUT"] = sourceAnalyze($GLOBALS["stdOUT"],true);
# Validate output (at least better than nothing)
$GLOBALS["stdOUT"] = validateText($GLOBALS["stdOUT"]);
# Flush the results
echo $GLOBALS["stdOUT"];

?>