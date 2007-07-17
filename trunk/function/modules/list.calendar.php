<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

if(RetrieveVar("calorientation","0111"))
     $_SESSION["calorientation"] = RetrieveVar("calorientation","0111");
elseif($_SESSION["calorientation"]=="")
     $_SESSION["calorientation"] = TimeStamp(0);

$time = TimeStamp2Time($_SESSION["calorientation"]);

$prevyear = date("YmdHis",mktime(0,0,0,date("m",$time),date("d",$time),date("Y",$time)-1));
$prevmonth = date("YmdHis",mktime(0,0,0,date("m",$time)-1,date("d",$time),date("Y",$time)));
$nextyear = date("YmdHis",mktime(0,0,0,date("m",$time),date("d",$time),date("Y",$time)+1));
$nextmonth = date("YmdHis",mktime(0,0,0,date("m",$time)+1,date("d",$time),date("Y",$time)));


$GLOBALS["calendar"]["month"] = date("m",$time);
$GLOBALS["calendar"]["start"] = date("YmdHis",mktime(0,0,0,date("m",$time),1,date("Y",$time)));
$GLOBALS["calendar"]["end"] = date("YmdHis",mktime(23,59,59,date("m",$time),date("t",$time),date("Y",$time)));
verbose("Orientiert an Timestamp: ".$time.", Monat ist ".$GLOBALS["calendar"]["month"]);
verbose("Monat geht von ".$GLOBALS["calendar"]["start"]." bis ".$GLOBALS["calendar"]["end"]);


$selection = $GLOBALS["restree"]["calendar"];
$selection1 = filterResources($selection,"to",$GLOBALS["calendar"]["start"],">=",null);
$selection1x = filterResources($selection1,"to",$GLOBALS["calendar"]["end"],"<=",null);
$selection2 = filterResources($selection,"from",$GLOBALS["calendar"]["end"],"<=",null);
$selection2x = filterResources($selection2,"from",$GLOBALS["calendar"]["start"],">=",null);
$selection = mergeSelections($selection1x,$selection2x);

for($i=0;$i<count($selection);$i++)
     $hasevent[date("j",TimeStamp2Time($selection[$i]["from"]))] = true;

append("<table border=\"1\" id=\"minical\" name=\"minical\">\n");
append("     <tr class=\"calnavi\">\n");
append("          <td>&nbsp;</td>\n");
append("          <td><a href=\"?module=calendar&calorientation=".$prevyear."\">&lt;&lt;</a></td>\n");
append("          <td><a href=\"?module=calendar&calorientation=".$prevmonth."\">&lt;</a></td>\n");
append("          <td colspan=\"3\">\n".
       "               <a href=\"?module=calendar&calrange=month\">\n".
       $GLOBALS["calendar"]["month"]." ".date("M",$time).
       "</a>\n".
       "          </td>\n");
append("          <td><a href=\"?module=calendar&calorientation=".$nextmonth."\">&gt;</a></td>\n");
append("          <td><a href=\"?module=calendar&calorientation=".$nextyear."\">&gt;&gt;</a></td>\n");
append("     </tr>\n");
append("     <tr class=\"calweekdays\">\n");
append("          <td>".
       "<a href=\"?module=calendar&calorientation=".
          date("YmdHis",mktime(0,0,0,date("m",$time),1,date("Y",$time)))."&calrange=week\">".
          date("W",mktime(0,0,0,date("m",$time),1,date("Y",$time)))."</a></td>\n");

$days = date("t",$time);
$first_weekday = date("w",TimeStamp2Time($GLOBALS["calendar"]["start"]))-(($_SESSION["week_start_on_sunday"])?0:1);

$inweek = 0; $i = 0;
$daycount = 1;
while($daycount<=$days || $inweek<7){
     if($inweek==7) {
          $orient = date("YmdHis",mktime(0,0,0,date("m",$time),$daycount,date("Y",$time)));
          append("     </tr>\n     <tr>\n");
          append("          <td class=\"calweeklink\"><a href=\"?module=calendar&calorientation=".
                 $orient."&calrange=week\" onclick=\"return ".
                 "AjaxGet(getElement('details'),'?module=calendar&".
                 "calorientation=".$orient."&calrange=week&ajax=1',null,null);\"><i>".
                 date("W",mktime(0,0,0,date("m",$time),$daycount,date("Y",$time))).
                 "</i></a></td>\n");
          $inweek = 0;
     }
     if($i>=$first_weekday && $daycount<=$days){
          if($hasevent[$daycount]) {
               $orient = date("YmdHis",mktime(0,0,0,date("m",$time),$daycount,date("Y",$time)));
               $class = "hasevent";
               $daylink = "<a href=\"?module=calendar&calorientation=".$orient.
                          "&calrange=day\" onclick=\"return AjaxGet(getElement('details'),'?module=calendar&".
                         "calorientation=".$orient."&calrange=day&ajax=1',null,'highlight');\">"
                         .($daycount++)."</a>";
          } else {
               $daylink = ($daycount++);
               $class ="noevent";
          }
          append("          <td class=\"".$class."\">".$daylink."</td>\n");
     }else
          append("          <td class=\"empty\">&nbsp;</td>\n");
     $inweek++;
     $i++;
}

append("     </tr>\n");
append("</table>\n");

/*
for($i=0;$i<count($selection);$i++){
     $item = $selection[$i];
     append("<div><div style=\"width: 100%;\">".$item["summary"]."</div></div>");
}
*/
/*
for($i=0;$i<count($GLOBALS["restree"]["calendar"]);$i++)
     append("<div><div style=\"width: 40%;\">".$GLOBALS["restree"]["calendar"][$i]["from"]." - ".$GLOBALS["restree"]["calendar"][$i]["to"]."</div><div style=\"width: 59%;\">".$GLOBALS["restree"]["calendar"][$i]["summary"]."</div></div>");
*/

?>