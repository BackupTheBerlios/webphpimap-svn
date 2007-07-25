<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

if(RetrieveVar("calorientation","0111"))
     $_SESSION["calorientation"] = RetrieveVar("calorientation","0111");
elseif($_SESSION["calorientation"]=="")
     $_SESSION["calorientation"] = TimeStamp(0);

if(RetrieveVar("calrange","0111"))
     $_SESSION["calrange"] = RetrieveVar("calrange","0111");
elseif($_SESSION["calrange"]=="")
     $_SESSION["calrange"] = "week";

$time = TimeStamp2Time($_SESSION["calorientation"]);

switch($_SESSION["calrange"]){
     case "month":
          $GLOBALS["calendar"]["month"] = date("m",$time);
          $GLOBALS["calendar"]["start"] = date("YmdHis",mktime(0,0,0,date("m",$time),1,date("Y",$time)));
          $GLOBALS["calendar"]["end"] = date("YmdHis",mktime(23,59,59,date("m",$time),date("t",$time),date("Y",$time)));
          verbose("Orientiert an Timestamp: ".$time.", Monat ist ".$GLOBALS["calendar"]["month"]);
          verbose("Monat geht von ".$GLOBALS["calendar"]["start"]." bis ".$GLOBALS["calendar"]["end"]);
     break;
     case "week":
          $GLOBALS["calendar"]["week"] = date("W",$time);
          $dayofweek = date("w",$time)-(($_SESSION["week_start_on_sunday"])?0:1);
          $GLOBALS["calendar"]["start"] = date("YmdHis",mktime(0,0,0,date("m",$time),date("d",$time)-$dayofweek,date("Y",$time)));
          $GLOBALS["calendar"]["end"] = date("YmdHis",mktime(23,59,59,date("m",$time),date("d",$time)-$dayofweek+6,date("Y",$time)));
          verbose("Orientiert an Timestamp: ".$time.", Woche ist ".$GLOBALS["calendar"]["week"]);
          verbose("Woche geht von ".$GLOBALS["calendar"]["start"]." bis ".$GLOBALS["calendar"]["end"]);
     break;
     case "day":
          $GLOBALS["calendar"]["start"] = date("YmdHis",mktime(0,0,0,date("m",$time),date("d",$time),date("Y",$time)));
          $GLOBALS["calendar"]["end"] = date("YmdHis",mktime(23,59,59,date("m",$time),date("d",$time),date("Y",$time)));
     break;
}

$selection = $GLOBALS["restree"]["calendar"];
$selection1 = filterResources($selection,"to",$GLOBALS["calendar"]["start"],">=",null);
$selection1x = filterResources($selection1,"to",$GLOBALS["calendar"]["end"],"<=",null);
$selection2 = filterResources($selection,"from",$GLOBALS["calendar"]["end"],"<=",null);
$selection2x = filterResources($selection2,"from",$GLOBALS["calendar"]["start"],">=",null);
$selection = mergeSelections($selection1x,$selection2x);
//$selection = intersectSelections($selection1x,$selection2x);
$selection = sortResources($selection,null,"from","ASC");

switch($_SESSION["calrange"]){
     case "month";
          for($i=0;$i<count($selection);$i++)
               $hasevent[date("j",TimeStamp2Time($selection[$i]["from"]))] = true;

          append("<table border=\"1\" width=\"100%\" height=\"100%\">\n");
          append("     <tr height=\"40\">\n");
          append("          <td colspan=\"8\"><h1>Termine im Monat ".date("M",$time)."</h1></td>");
          append("     </tr>\n");
          append("     <tr height=\"25\">\n");
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
          append("     <tr height=\"25\">\n");
          append("          <td>&nbsp;</td>\n");
          append("          <td>Mo</td>\n");
          append("          <td>Di</td>\n");
          append("          <td>Mi</td>\n");
          append("          <td>Do</td>\n");
          append("          <td>Fr</td>\n");
          append("          <td>Sa</td>\n");
          append("          <td>So</td>\n");
          append("     </tr>\n");
          append("     <tr>\n");
          append("          <td>".
                    "<a href=\"?module=calendar&calorientation=".
                    date("YmdHis",mktime(0,0,0,date("m",$time),1,date("Y",$time)))."&calrange=week\"><i>".
                    date("W",mktime(0,0,0,date("m",$time),1,date("Y",$time)))."</i></a></td>\n");

          $days = date("t",$time);
          $first_weekday = date("w",TimeStamp2Time($GLOBALS["calendar"]["start"]))-(($_SESSION["week_start_on_sunday"])?0:1);

          $inweek = 0; $i = 0;
          $daycount = 1;
          while($daycount<=$days || $inweek<7){
               if($inweek==7) {
                    append("     </tr>\n     <tr>\n");
                    append("          <td><a href=\"?module=calendar&calorientation=".
                              date("YmdHis",mktime(0,0,0,date("m",$time),$daycount,date("Y",$time))).
                              "&calrange=week\"><i>".date("W",mktime(0,0,0,date("m",$time),$daycount,date("Y",$time))).
                              "</i></a></td>\n");
                    $inweek = 0;
               }
               if($i>=$first_weekday && $daycount<=$days){
                    if($hasevent[$daycount])
                         $daylink = "<b><a href=\"?module=calendar&calorientation=".date("YmdHis",mktime(0,0,0,date("m",$time),$daycount,date("Y",$time)))."&calrange=day\">".($daycount++)."</a></b>";
                    else
                         $daylink = ($daycount++);
                    append("          <td>".$daylink."</td>\n");
               }else
                    append("          <td>&nbsp;</td>\n");
                    $inweek++;
                    $i++;
          }

          append("     </tr>\n");
          append("</table>\n");

     break;
     case "week":
          append("<h1>Termine in Woche ".$GLOBALS["calendar"]["week"]."</h1>");
          append("<table border=\"0\" width=\"100%\">\n");
          for($i=0;$i<count($selection);$i++){
               $item = $selection[$i];
               append("<tr>");
               append("<td width=\"20%\">".readableTS($item["from"],"date")."</td>");
               append("<td width=\"20%\">von ".readableTS($item["from"],"time")."<br />bis ".
                       readableTS($item["to"],"time")."</td>");
               append("<td>".$item["summary"]."</td>");
               append("</tr>");
          }
          append("</table>");
     break;
     case "day":
          append("<h1>Termine am ".readableTS($_SESSION["calorientation"],"date")."</h1>");
          $base_tpl = file_get_contents(designroot."/display/item.calendar.list.html");
          for($i=0;$i<count($selection);$i++){
               append(replaceFields($base_tpl,"calendar",$selection[$i]));
          }

          /*
          append("<table border=\"0\" width=\"100%\">\n");
          for($i=0;$i<count($selection);$i++){
               $item = $selection[$i];
               append("<tr>");
               append("<td width=\"20%\">".readableTS($item["from"],"date")."</td>");
               append("<td width=\"20%\">von ".readableTS($item["from"],"time")."<br />bis ".
                         readableTS($item["to"],"time")."</td>");
               append("<td>".$item["summary"]."</td>");
               append("</tr>");
          }
          append("</table>");
          */
     break;
}




?>