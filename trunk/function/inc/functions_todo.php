<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

function analyseResourceTodoXML($folder){

}

function analyseResourceTodo($folder){
     $d = opendir($folder);
     while($f=readdir($d))
          if($f!="." && $f!=".."){
               $pimfile = str_replace("//","/",$folder."/".$f);
               $fp = fopen($pimfile,"r");
               $c = "";
               while(!feof($fp)) $c .= fgets($fp,9999);
               fclose($fp);
               $c = str_replace("\r","\n",$c); $c = str_replace("\n\n","\n",$c);
               verbose("Analysing '".$pimfile."'");
               $c = explode("\n",$c);
               for($i=0;$i<count($GLOBALS["fields"]["todo"]);$i++)
                    if($GLOBALS["fields"]["todo"][$i]!="pimfile")
                         ${$GLOBALS["fields"]["todo"][$i]} = "";
               for($i=0;$i<count($c);$i++){
                    if(strtoupper(substr($c[$i],0,4))=="UID:"){
                         $x = explode(":",$c[$i]);
                         $uid = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,3))=="DUE"){
                         $x = explode(":",$c[$i]);
                         $due = checkTimeStamp(str_replace("T","",str_replace("Z","",$x[1])));
                    }
                    if(strtoupper(substr($c[$i],0,7))=="SUMMARY"){
                         $x = explode(":",$c[$i]);
                         $summary = $x[1];
                    }
               }
               $id = count($GLOBALS["restree"]["todo"]);
               for($i=0;$i<count($GLOBALS["fields"]["todo"]);$i++)
                    $GLOBALS["restree"]["todo"][$id][$GLOBALS["fields"]["todo"][$i]] = ${$GLOBALS["fields"]["todo"][$i]};
               $GLOBALS["restree"]["todo"][$id][internalid] = $id;
          }
}

?>