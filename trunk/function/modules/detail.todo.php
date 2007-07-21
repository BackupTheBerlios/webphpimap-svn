<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

$id = RetrieveVar("id","0111");
$item = $GLOBALS["restree"]["todo"][$id];
if($id=="" OR $item==""){
     append("<h1>Anstehende Aufgaben</h1>");
     $selection = $GLOBALS["restree"]["todo"];
     //$selection = filterResources($GLOBALS["restree"]["todo"],"due",TimeStamp(0),">",null);
     $selection = sortResources($selection,null,"due","ASC");
     for($i=0;$i<count($selection);$i++){
          $item = $selection[$i];
          append("<li><a href=\"?module=todo&id=".$item[internalid]."\">".$item["summary"]."</a></li>");
     }
}else {
     $tpl = file_get_contents(designroot."/display/item.todo.html");
     $tpl = replaceFields($tpl,"todo",$item);
     append($tpl);

}




?>