<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

$id = RetrieveVar("id","0110");
$item = $GLOBALS["restree"]["contact"][$id];
if($id=="" OR $item=="") append("Sorry, a Contact with this ID does not exist!");
else {
     for($i=0;$i<count($fields["contact"]);$i++)
          append("<div style=\"width: 25%; float: left;\">".$fields["contact"][$i]."</div>".
               "<div style=\"width: 74%; float: left;\">".$item[$fields["contact"][$i]]."&nbsp;</div>");


}



?>