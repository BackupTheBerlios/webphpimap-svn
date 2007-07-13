<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

$id = RetrieveVar("id","0110");
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
     for($i=0;$i<count($fields["todo"]);$i++)
          append("<div style=\"width: 25%; float: left;\">".$fields["todo"][$i]."</div>".
                    "<div style=\"width: 74%; float: left;\">".$item[$fields["todo"][$i]]."&nbsp;</div>");


}




?>