<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if(phPIMap!="ok") die("Direct access to this location is not allowed");

append("<h1>Preparing Synchronisation</h1>");

unset($GLOBALS["restree"]);
$pimbox = "{".imap_host.":".imap_port."/imap}";
append("Opening PIMBox on ".$pimbox."<br />");

for($i=0;$i<count($types);$i++)
     for($z=0;$z<count($resources[$types[$i]]);$z++){
          $box[$types[$i]][$z] = imap_open($pimbox.$resources[$types[$i]][$z],imap_user,imap_pw) or die($pimbox.$resources[$types[$i]][$z]);
          append("Opened PIMBox for '".$types[$i]."/#".$z." as '".$pimbox.$resources[$types[$i]][$z]."'<br />");
     }

for($i=0;$i<count($types);$i++){
     $res = $types[$i];
     for($z=0;$z<count($resources[$res]);$z++){
          $norefresh = RetrieveVar("norefresh","1111");
          if(!$norefresh){
               $pbox = &$box[$res][$z];
               $info = imap_check($pbox);
               $mailamount = $info->Nmsgs;
               $list = imap_fetch_overview($pbox,"1:".$mailamount,0);
               emptyMirrorDir(mirror."/".$resources[$res][$z]);
               createMirrorDir(mirror."/".$resources[$res][$z]);
               append("Preparing to read ".$mailamount." Mails from PIMBox '".$types[$i]."/#".$z."'<br />");
               for($y=0;$y<$mailamount;$y++){
                    $subject = $list[$y]->subject;
                    $mail = imap_body($pbox,$list[$y]->uid,FT_UID);
                    $f = @fopen(mirror."/".$resources[$res][$z]."/".$subject,"w");
                    @fputs($f,$mail);
                    @fclose($f);
               }
               append("Synced '".($y)."' Items for Resource Type '".$res."'<br />\n");
          }
          analyseResource(mirror."/".$resources[$res][$z]."/",$res);
     }
}

$f = fopen(restree,"w");
fputs($f,"<?\n\n\$GLOBALS[\"restree\"] = String2Array(\"".Array2String($GLOBALS["restree"])."\");\n\n?>");
fclose($f);

?>