<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

$selection = sortResources($GLOBALS["restree"]["contact"],null,"surname","ASC");
for($i=0;$i<count($selection);$i++){
     $item = $selection[$i];
     append("<div><a href=\"?module=contact&id=".$item[internalid]."\" ".
               "onclick=\"return AjaxGet(getElement('details'),'?module=contact&".
               "id=".$item[internalid]."&ajax=1',null,'highlight');\">
                <div style=\"width: 49%;\">".$item["surname"]."</div>
               <div style=\"width: 50%;\">".$item["firstname"]."</div>
             </a></div>");
}
/*
for($i=0;$i<count($GLOBALS["restree"]["contact"]);$i++)
     append("<div><div style=\"width: 49%;\">".$GLOBALS["restree"]["contact"][$i]["surname"]."</div><div style=\"width: 50%;\">".$GLOBALS["restree"]["contact"][$i]["firstname"]."</div></div>");
*/

?>