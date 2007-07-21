<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

$id = RetrieveVar("id","0111");
$item = $GLOBALS["restree"]["contact"][$id];
if($id=="" OR $item=="") append("Sorry, a Contact with this ID does not exist!");
else {
     $fields = $GLOBALS["fields"];
     $tpl = file_get_contents(designroot."/display/item.contact.html");
     $tpl = replaceFields($tpl,"contact",$item);
     append($tpl);
}



?>