<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

$selection = $GLOBALS["restree"]["todo"];
//$selection = filterResources($GLOBALS["restree"]["todo"],"due",TimeStamp(0),">",null);
//$selection = sortResources($selection,null,"due","DESC");
for($i=0;$i<count($selection);$i++){
     $item = $selection[$i];
     append("<div><div style=\"width: 100%;\">".
               "<a href=\"?module=todo&id=".$item[internalid]."\" ".
               "onclick=\"return AjaxGet(getElement('details'),'?module=todo&".
               "id=".$item[internalid]."&ajax=1',null,'highlight');\">".$item["summary"]."</a>".
            "</div></div>");
}
/*
for($i=0;$i<count($GLOBALS["restree"]["todo"]);$i++)
     append("<div><div style=\"width: 20%;\">".$GLOBALS["restree"]["todo"][$i]["due"]."</div><div style=\"width: 79%;\">".$GLOBALS["restree"]["todo"][$i]["summary"]."</div></div>");
*/

?>