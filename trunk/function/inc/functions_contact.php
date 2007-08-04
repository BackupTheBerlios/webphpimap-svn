<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

function analyseResourceContactXML($folder){

}


function analyseResourceContact($folder){
     $d = opendir($folder);
     while($f=readdir($d))
          if($f!="." && $f!=".." && str_replace(".svn","",$f)!=""){
               $pimfile = str_replace("//","/",$folder."/".$f);
               $fp = fopen($pimfile,"r");
               $c = "";
               while(!feof($fp)) $c .= fgets($fp,9999);
               fclose($fp);
               $c = str_replace("\r","\n",$c); $c = str_replace("\n\n","\n",$c);
               verbose("Analysing '".$pimfile."'");
               $c = explode("\n",$c);
               for($i=0;$i<count($GLOBALS["fields"]["contact"]);$i++)
                    if($GLOBALS["fields"]["contact"][$i]!="pimfile")
                         ${$GLOBALS["fields"]["contact"][$i]} = "";
               for($i=0;$i<count($c);$i++){
                    if(strtoupper(substr($c[$i],0,4))=="UID:"){
                         $x = explode(":",$c[$i]);
                         $uid = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,2))=="N:"){
                         $x = explode(":",$c[$i]);
                         $x = explode(";",$x[1]);
                         $surname= $x[0];
                         $firstname = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,5))=="BDAY:"){
                         $x = explode(":",$c[$i]);
                         $birthday = checkTimeStamp(str_replace("T","",str_replace("Z","",$x[1])));
                    }
                    if(strtoupper(substr($c[$i],0,6))=="EMAIL:"){
                         $x = explode(":",$c[$i]);
                         $email = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,4))=="URL:"){
                         $x = explode(":",$c[$i]);
                         $url = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,6))=="TITLE:"){
                         $x = explode(":",$c[$i]);
                         $title = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,4))=="ORG:"){
                         $x = explode(":",$c[$i]);
                         $organization = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,5))=="NOTE:"){
                         $x = explode(":",$c[$i]);
                         $note = $x[1];
                    }
                    if(strtoupper(substr($c[$i],0,4))=="TEL;"){
                         $x = explode(";",$c[$i]);
                         for($y=0;$y<count($x);$y++){
                              $nr = explode(":",$x[$y]);
                              switch($nr[0]){
                                   case "TYPE=HOME":
                                        $telephone = $nr[1];
                                   break;
                                   case "TYPE=VOICE":
                                        $mobilephone = $nr[1];
                                   break;
                                   case "TYPE=CELL":
                                        $cellphone = $nr[1];
                                   break;
                                   case "TYPE=FAX":
                                        $fax = $nr[1];
                                   break;
                              }
                         }
                    }
               }
               $id = count($GLOBALS["restree"]["contact"]);
               for($i=0;$i<count($GLOBALS["fields"]["contact"]);$i++)
                    $GLOBALS["restree"]["contact"][$id][$GLOBALS["fields"]["contact"][$i]] = ${$GLOBALS["fields"]["contact"][$i]};
               $GLOBALS["restree"]["contact"][$id][internalid] = $id;
          }
}

?>