<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

# Before PIM-Resources are downloaded from IMAP the mirror-dir is removed
function emptyMirrorDir($fullpath){
     $d=@opendir($fullpath);
     while($file=@readdir($d)){
          if($file=="." || $file=="..") continue;
          if(is_dir($fullpath."/".$file)) emptyMirrorDir($fullpath."/".$file);
          else unlink($fullpath."/".$file);
     }
     @closedir($d);
     @rmdir($fullpath);
}

# After removing mirror-dir it must be renewed including parents
function createMirrorDir($fullpath){
     //echo "Attempting to create ".$fullpath."<br />\n";
     $path = explode("/",$fullpath);
     $tpath = "";
     for($i=0;$i<count($path);$i++){
          $tpath .= $path[$i]."/";
          if(!file_exists($tpath)){
               $chk = @mkdir($tpath,0777);
               verbose("Creating ".$tpath." '".($chk?"successfully":"unsuccessfully")."'");
          }
     }
}

# Resources are analysed
function analyseResource($folder,$type){
     switch($type){
          case "contact":
               analyseResourceContact($folder);
          break;
          case "calendar":
               analyseResourceCalendar($folder);
          break;
          case "todo":
               analyseResourceTodo($folder);
          break;
          default: die("Fatal Exception: Unknown Resource Type.");
     }
}

# This function is used to append Output
function append($text){
     $GLOBALS["stdOUT"] .= $text."\n";
}

# This function appends or echoes Errors and Notices
# Have a look at config.php to switch it on/off
function verbose($text){
     if(verboseMode=="true") append("<i>".$text."</i><br />\n");
     if(verboseHead=="true") echo "<i>".$text."</i><br />\n";
}
function verboseFail($text,$success){
     if($success) verbose($text);
     else echo "<span class=\"error\">".$text."</span>\n";
}

# This function is used to store the Resources-Tree as a String within a variable
# The string is saved in a file below the user-dir and can be parsed fast
function Array2String($Array) {
     if(!is_array($Array)) return null;
     $Return='';
     $NullValue="^^^";
     foreach ($Array as $Key => $Value) {
          if(is_array($Value))
               $ReturnValue='^^array^'.Array2String($Value);
          else
               $ReturnValue=(strlen($Value)>0)?$Value:$NullValue;
          $Return.=urlencode(base64_encode($Key)) . '|' . urlencode(base64_encode($ReturnValue)).'||';
     }
     return urlencode(substr($Return,0,-2));
}

# This Function returns the Array which was stored as string
function String2Array($String) {
     if($String=="") return null;
     $Return=array();
     $String=urldecode($String);
     $TempArray=explode('||',$String);
     $NullValue=urlencode(base64_encode("^^^"));
     foreach ($TempArray as $TempValue) {
          list($Key,$Value)=explode('|',$TempValue);
          $DecodedKey=base64_decode(urldecode($Key));
          if($Value!=$NullValue) {
               $ReturnValue=base64_decode(urldecode($Value));
               if(substr($ReturnValue,0,8)=='^^array^')
                    $ReturnValue=String2Array(substr($ReturnValue,8));
               $Return[$DecodedKey]=$ReturnValue;
          }
          else
               $Return[$DecodedKey]=NULL;
     }
     return $Return;
}

# This function can filter Resources and returns an array with the filtered ones
function filterResources($array,$field,$pattern,$comparator,$subselection){
     if($subselection==null) $subselection = @array_keys($array);
     $pattern = addslashes($pattern);
     for($i=0;$i<count($subselection);$i++){
          $val = addslashes($array[$subselection[$i]][$field]);
          $res = false;
          switch($comparator){
               case "startswith":
                    if(substr($val,0,strlen($pattern))==$pattern) $res = true;
               break;
               case "endswith":
                    if(substr($val,strlen($val)-strlen($pattern))==$pattern) $res = true;
               break;
               case "instr":
                    if(!strpos($pattern,$val)===false) $res = true;
               break;
               default:
                    eval("\$res = ('".$val."'".$comparator."'".$pattern."')?true:false;");
          }
          if($res) $results[] = $subselection[$i];
          //verbose("Compare '".$val."' ".$comparator." '".$pattern."' (".(($res)?"ok":"not ok").")");
     }
     for($i=0;$i<count($results);$i++)
          $narray[] = $array[$results[$i]];
     return $narray;
}

# This function merges two selections of items. You should not mix different PIM-Types
# however, this would be possible
function mergeSelections($selection1,$selection2){
     $foundsids[] = "zzz";
     for($i=0;$i<count($selection1);$i++){
          if(!in_array($selection1[$i][internalid],$foundsids))
               $merger[] = $selection1[$i];
          $foundsids[] = $selection1[$i][internalid];
     }
     for($i=0;$i<count($selection2);$i++){
          if(!in_array($selection2[$i][internalid],$foundsids))
               $merger[] = $selection2[$i];
          $foundsids[] = $selection2[$i][internalid];
     }
     return $merger;
}

# This function returns the interesection of two arrays as new array
function intersectSelections($selection1,$selection2){
     for($i=0;$i<count($selection1);$i++){
          $foundsids[] = $selection1[$i][internalid];
     }
     for($i=0;$i<count($selection2);$i++){
          if(in_array($selection2[$i][internalid],$foundsids))
               $intersection[] = $selection2[$i];
     }
     return $intersection;
}

# This function sorts Resources
function sortResources($array,$subselection,$field,$direction){
     if($subselection==null) $subselection = @array_keys($array);
     for($i=0;$i<count($subselection);$i++)
          $sorter[] = strtolower($array[$subselection[$i]][$field])."_".$subselection[$i];

     switch($direction){
          case "ASC":
               @sort($sorter);
          break;
          case "DESC":
               @rsort($sorter);
          break;
     }

     for($i=0;$i<count($sorter);$i++){
          $it = explode("_",$sorter[$i]);
          $it = $it[count($it)-1];
          $sortedindizes[] = $it;
     }


     for($i=0;$i<count($sortedindizes);$i++)
          $narray[] = $array[$sortedindizes[$i]];

     return $narray;
}


function Datum(){
     return date("Y")."-".date("m")."-".date("d");
}

function Zeit(){
     return date("H").":".date("i").":00";
}

function TimeStamp($MinusMinutes){
     $jetzt = getdate();
     return gmdate("YmdHis",mktime($jetzt["hours"],$jetzt["minutes"]-$MinusMinutes,$jetzt["seconds"],$jetzt["mon"],$jetzt["mday"],$jetzt["year"]));
     //return strftime("20%y%m%d%H%M%S",$d);
}

function TimeStamp2Time($ts){
     for($i=0;$i<strlen($ts);$i++){
          if($i<4) $year .= $ts[$i];
          elseif($i<6) $month .= $ts[$i];
          elseif($i<8) $day .= $ts[$i];
          elseif($i<10) $hour .= $ts[$i];
          elseif($i<12) $min .= $ts[$i];
          elseif($i<14) $sec .= $ts[$i];
     }
     //echo $year." ".$month." ".$day." ".$hour." ".$min." ".$sec;
     $time = mktime($hour,$min,$sec,$month,$day,$year);
     return $time;
}

function checkTimeStamp($ts){
     for($i=0;$i<14;$i++)
          if($ts[$i]!="") $ret .= $ts[$i]; else $ret .= "0";
     return $ret;
}

function readableTS($ts,$rettype){
     $Y = substr($ts,0,4);
     $M = substr($ts,4,2);
     $D = substr($ts,6,2);
     $h = substr($ts,8,2);
     $m = substr($ts,10,2);
     $s = substr($ts,12,2);
     $date["time"] = $h.":".$m.":".$s;
     $date["date"] = $D.".".$M.".".$Y;
     return $date[($rettype!="")?$rettype:$date];
}

# This function reads passed vars
# First we check session, post, get and cookie-vars.
# $pattern offers the possibility just to check several, e.g. 0110 would just
# check post and get
function RetrieveVar($Varname,$pattern){
     if($pattern[0]=="1" && isset($_SESSION[$Varname])): return $_SESSION[$Varname];
     elseif($pattern[1]=="1" && isset($_POST[$Varname])): return $_POST[$Varname];
     elseif($pattern[2]=="1" && isset($_GET[$Varname])): return $_GET[$Varname];
     elseif($pattern[3]=="1" && isset($_COOKIE[$Varname])): return $_COOKIE[$Varname];
     else: return false;
     endif;
}

# This replaces the values of a specific item in a template
function replaceFields($tpl,$restype,$item){
     $fields = $GLOBALS["fields"][$restype];
     for($i=0;$i<count($fields);$i++)
          $tpl = str_replace("~".$fields[$i]."~",$item[$fields[$i]],$tpl);
     return $tpl;
}

?>