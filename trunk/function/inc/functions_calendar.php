<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

function analyseResourceCalendar($folder){
     verbose("Analyzing Resources in Folder '".$folder."'");
     $d = opendir($folder);
     while($f=readdir($d))
          if($f!="." && $f!=".."){
               $pimfile = str_replace("//","/",$folder."/".$f);
               $fp = fopen($pimfile,"r");
               $c = "";
               while(!feof($fp)) $c .= fgets($fp,9999);
               fclose($fp);
               if(strpos($c,"<?xml")>0) $ar = analyseResourceCalendarXML($c,$pimfile);
               else $ar = analyseResourceCalendarVCard($c,$pimfile);

               $id = count($GLOBALS["restree"]["calendar"]);
               for($i=0;$i<count($GLOBALS["fields"]["calendar"]);$i++)
                    $GLOBALS["restree"]["calendar"][$id][$GLOBALS["fields"]["calendar"][$i]] = $ar[$GLOBALS["fields"]["calendar"][$i]];
               $GLOBALS["restree"]["calendar"][$id][internalid] = $id;
          }
}

function analyseResourceCalendarVCard($string,$pimfile){
     verbose("Analyzing Resource Calendar VCard for File '".$pimfile."'");
     $c = str_replace("\r","\n",$string); $c = str_replace("\n\n","\n",$c);
     verbose("Analysing '".$pimfile."'");
     $c = explode("\n",$c);
     for($i=0;$i<count($GLOBALS["fields"]["calendar"]);$i++)
          if($GLOBALS["fields"]["calendar"][$i]!="pimfile")
               ${$GLOBALS["fields"]["calendar"][$i]} = "";
     for($i=0;$i<count($c);$i++){
          if(strtoupper(substr($c[$i],0,4))=="UID:"){
               $x = explode(":",$c[$i]);
               $ar["uid"] = $x[1];
          }
          if(strtoupper(substr($c[$i],0,7))=="DTSTART"){
               $x = explode(":",$c[$i]);
               $ar["from"] = checkTimeStamp(str_replace("T","",str_replace("Z","",$x[1])));
          }
          if(strtoupper(substr($c[$i],0,5))=="DTEND"){
               $x = explode(":",$c[$i]);
               $ar["to"] = checkTimeStamp(str_replace("T","",str_replace("Z","",$x[1])));
          }
          if(strtoupper(substr($c[$i],0,7))=="SUMMARY"){
               $x = explode(":",$c[$i]);
               $ar["summary"] = $x[1];
          }
     }
     return $ar;
}

function analyseResourceCalendarXML($string,$pimfile){
     verbose("Analyzing Resource Calendar XML for File '".$pimfile."'");
     $translator_phPIMap = Array("pimfile","uid","summary","from","to");
     $translator_XML = Array($pimfile,"uid","summary","start-date","end-date");
     $xmlread = new MiniXMLDoc();
     $xmlread->fromString($string);
     $rootArray = $xmlread->toArray();
     print_r($rootArray);
     unset($GLOBALS["tmp"]["flattenArray"]);
     $ar = flattenArray($rootArray);
     print_r($ar);
     for($i=0;$i<count($translator_XML);$i++)
          $ar[$translator_phPIMap[$i]] = $rootArray[$translator_XML[$i]];
     return $ar;
}

function flattenArray($subarray){
     $keys = @array_keys($subarray);
     for($i=0;$i<count($keys);$i++) {
          if(is_array($subarray[$keys[$i]])) flattenArray($subarray[$keys[$i]]);
          else $GLOBALS["tmp"]["flattenArray"][$keys[$i]] = $subarray[$keys[$i]];
     }
}

?>