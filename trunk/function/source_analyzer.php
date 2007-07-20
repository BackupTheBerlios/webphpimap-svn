<?

function sourceAnalyze($SOLVED_TEXT,$globals){
    $State = "app";

    $i = 0;
    $SearchStrings[$i++] = "~designroot~";              // 0
    $SearchStrings[$i++] = "~sessionid~";               // 1
    $SearchStrings[$i++] = "~mainmodule~";              // 2
    $SearchStrings[$i++] = "~application~";             // 3
    $SearchStrings[$i++] = "~/application~";            // 4
    $SearchStrings[$i++] = "~ignore~";                  // 5
    $SearchStrings[$i++] = "~/ignore~";                 // 6
    $SearchStrings[$i++] = "~mainpage~";                // 7
    $SearchStrings[$i++] = "~/mainpage~";               // 8
    $SearchStrings[$i++] = "~domain~";                  // 9

    $itemamount = 1;
    while($itemamount>0) {
         $IndexPage = $SOLVED_TEXT;

        unset($AllPositions);
        $AllPositions[0] = 0;
        $i = 1;
        for($z = 0; $z<count($SearchStrings); $z++){
             $LastPos = 0;
             while(strpos($IndexPage,$SearchStrings[$z],$LastPos)!=""){
                  $AllPositions[$i] = strpos($IndexPage,$SearchStrings[$z],$LastPos);
                  $LastPos = $AllPositions[$i]+1;
                  $i++;
             }
        }
        $AllPositions[count($AllPositions)] = strlen($IndexPage);

        // Sortieralgorithmus der gespeicherten Positionen
        sort($AllPositions);
        unset($Log);
        $zPos = 0;
        $z = 0;
        $Weiter = true;
        while($Weiter){
             $Item = substr($IndexPage,$AllPositions[$zPos],$AllPositions[$zPos+1]-$AllPositions[$zPos]);
             for($x = 0; $x<count($SearchStrings); $x++){
                  $XFnd = false;
                  if(strpos(" ".$Item,$SearchStrings[$x])>0):
                       $Log[$z++] = $SearchStrings[$x];
                       $Log[$z++] = str_replace($SearchStrings[$x],"",$Item);
                       $XFnd = true;
                       break;
                   endif;
             }
             if(!$XFnd): $Log[$z++] = $Item; endif;
             $zPos++;
             if($zPos>count($AllPositions)):
                  $Weiter = false;
             endif;
        }

        // Sichere derzeitige GLOBAL
        if(!$globals): $SECURE_stdOUT = $GLOBALS["stdOUT"]; endif;

        // Wenn keine Keywords mehr vorhanden Schleife abbrechen
        // Wenn Wert auf false BLEIBT wird die Schleife abgebrochen
        // Wird er auf TRUE gesetzt, geht die Schleife weiter.
        // Wegen den Keywords IGNORE wird die Variable SOURCE_ROUND direkt
        // in der Schleife auf TRUE gesetzt.

        $SOLVED_TEXT = "";
        $LogAnz = 0;
        $itemamount = 0;
        while($LogAnz<count($Log)){
             $GLOBALS["stdOUT"] = "";
             if(!in_array($Log[$LogAnz],$SearchStrings) OR $Log[$LogAnz]==$SearchStrings[15]):
                  if($Log[$LogAnz]!=$SearchStrings[15]):
                       $GLOBALS["stdOUT"] .=  $Log[$LogAnz];
                       $GLOBALS["stdOUT"] = translateText($GLOBALS["stdOUT"]);
                       $GLOBALS["stdOUT"] = cloakURL($GLOBALS["stdOUT"]);
                       // Mailadressen nur deformieren, wenn stdOUT gewandelt wird. (In Mails ist die JavaScript-Funktion nicht vorhanden)
                       if($globals): $GLOBALS["stdOUT"] = deformateMail($GLOBALS["stdOUT"]); endif;
                  else:
                       while($Log[$LogAnz]!=$SearchStrings[16]){
                            $GLOBALS["stdOUT"] .= $Log[$LogAnz];
                            $LogAnz++;
                       }
                       $GLOBALS["stdOUT"] .= $Log[$LogAnz];
                  endif;
             else:
                 $itemamount++;
                 switch($Log[$LogAnz]){
                      case $SearchStrings[0]: // designroot
                           $GLOBALS["stdOUT"] .= $GLOBALS["Header"]->Root;
                      break;
                      case $SearchStrings[1]; // sessionid
                           $GLOBALS["stdOUT"] .= session_id();
                      break;
                      case $SearchStrings[2]: // mainmodule
                           $GLOBALS["stdOUT"] .= "~mainpage~".$GLOBALS["inc_main"]."&State=main~/mainpage~";
                      break;
                      case $SearchStrings[3]: // application
                           $orig = $State;
                           $State = "app";
                           if(strpos($Log[$LogAnz+1],"=")>0):
                               $url = explode("&",$Log[$LogAnz+1]);
                               for($i=0;$i<count($url);$i++){
                                    $va = explode("=",$url[$i]);
                                    $_ORIGS[$va[0]] = $_POST[$va[0]];
                                    $_POST[$va[0]] = $va[1];
                                    if($va[0]=="State"): $State = $va[1]; endif;
                               }
                               $app = $_POST["inc"];
                           else:
                               $app = $Log[$LogAnz+1];
                           endif;
                           if(!include($GLOBALS["_MODULES"][$app][1])): $GLOBALS["stdOUT"] .=  "Fehler bei Aufruf der Seite '".$GLOBALS["_MODULES"][$app][0]."'<br>"; endif;
                           $State = $orig;
                           if(strpos($Log[$LogAnz+1],"=")>0):
                               for($i=0;$i<count($url);$i++){
                                    $va = explode("=",$url[$i]);
                                    $_POST[$va[0]] = $_ORIGS[$va[0]];
                               }
                               unset($_ORIGS);
                           endif;
                           $LogAnz = $LogAnz+2;
                      break;
                      case $SearchStrings[7]: // mainpage
                           $orig = $GLOBALS["State"];
                           $State = "main";
                           if(strpos($Log[$LogAnz+1],"=")>0):
                               $url = explode("&",$Log[$LogAnz+1]);
                               for($i=0;$i<count($url);$i++){
                                    $va = explode("=",$url[$i]);
                                    $_ORIGS[$va[0]] = $_POST[$va[0]];
                                    $_POST[$va[0]] = $va[1];
                                    if($va[0]=="State"): $State = $va[1]; endif;
                                    if($va[0]=="inc"): $app = $va[1]; endif;
                               }
                           else:
                               $app = $Log[$LogAnz+1];
                           endif;
                           if(!include($GLOBALS["_MODULES"][$app][1])): $GLOBALS["stdOUT"] .=  "Fehler bei Aufruf der Seite '".$GLOBALS["_MODULES"][$app][0]."'<br>"; endif;
                           $GLOBALS["_PAGEAPP"] = ($app!="")?$app:"Default Module";
                           $GLOBALS["State"] = $orig;
                           if(count($_ORIGS)>0):
                               for($i=0;$i<count($url);$i++){
                                    $va = explode("=",$url[$i]);
                                    $_POST[$va[0]] = $_ORIGS[$va[0]];
                               }
                               unset($_ORIGS);
                           endif;
                           $LogAnz = $LogAnz+1;
                      break;
                      case $SearchStrings[9]: // domain
                           $GLOBALS["stdOUT"] .=  domain;
                      break;
                 }
                 // Hier knnen Operationen an stdOUT erfolgen. (ist in diesem Fall nur zustzliches Item)
                 // Hier ist ignoring false
                 $GLOBALS["stdOUT"] = translateText($GLOBALS["stdOUT"]);
                 $GLOBALS["stdOUT"] = cloakURL($GLOBALS["stdOUT"]);
                 // Mailadressen nur deformieren, wenn stdOUT gewandelt wird. (In Mails ist die JavaScript-Funktion nicht vorhanden)
                 if($globals): $GLOBALS["stdOUT"] = deformateMail($GLOBALS["stdOUT"]); endif;
             endif;

             $SOLVED_TEXT .= $GLOBALS["stdOUT"];
             $LogAnz++;
        }
        if(RetrieveVar("CASCADE")):
              echo "#".($GLOBALS["CASC_AMNT"]++).":<hr>\n\n".$SOLVED_TEXT."<br>Itemcount: ".$itemamount."<br>\n\n<hr>\n\n";
        endif;
    }
    // Backsave GLOBAL
    if(!$globals): $GLOBALS["stdOUT"] = $SECURE_stdOUT; endif;
    return $SOLVED_TEXT;
}

function deformateMail($SOLVED_TEXT){
    $mail_arr = Array("\""," ","\'","<",">");
    $p=0; $weiter = true; $i=0;
    while($weiter){
        $p = strpos($SOLVED_TEXT,"mailto:",$p);
        if($p>0):
            $stop = false;
            $i_e = $p;
            while(!$stop){
                $char_e = substr($SOLVED_TEXT,$i_e,1);
                if(!in_array($char_e,$mail_arr)): $i_e++; else: $stop = true; endif;
            }
            $mailstart = $p+strlen("mailto:");
            $maillength = $i_e-$mailstart;
            $part[0] = substr($SOLVED_TEXT,0,$p);
            $part[1] = substr($SOLVED_TEXT,$mailstart,$maillength);
            $part[2] = substr($SOLVED_TEXT,$i_e);
            $mail = explode("@",$part[1]);
            $part[1] = "javascript:openMail('".$mail[0]."','".$mail[1]."');";
            $SOLVED_TEXT = $part[0].$part[1].$part[2];
        else: $weiter = false; endif;
        $i++;
    }
    $p=0; $weiter = true; $i=0;
    return $SOLVED_TEXT;
}

function translateText($SOLVED_TEXT){
    $len = strlen("~translate~");
    $len_e = strlen("~/translate~");

    if(substr_count($SOLVED_TEXT,"~translate~")==substr_count($SOLVED_TEXT,"~/translate~")):
        while(strpos($SOLVED_TEXT,"~translate~")>0){
             $Part1 = substr($SOLVED_TEXT,0,strpos($SOLVED_TEXT,"~translate~"));
             $LangElement = substr($SOLVED_TEXT,strpos($SOLVED_TEXT,"~translate~")+$len,strpos($SOLVED_TEXT,"~/translate~")-(strpos($SOLVED_TEXT,"~translate~")+$len));
             $Part2 = substr($SOLVED_TEXT,strpos($SOLVED_TEXT,"~/translate~")+$len_e);
             if($GLOBALS["stdLANG"][$LangElement]!=""): $LangElement = $GLOBALS["stdLANG"][$LangElement]; endif;
             $SOLVED_TEXT = $Part1.$LangElement.$Part2;
         }
    else:
        echo "Wrong language-Replacer Count:<br><br>";
        echo "Starter Marks: ".substr_count($SOLVED_TEXT,"~translate~")."<br>";
        echo "End Marks: ".substr_count($SOLVED_TEXT,"~/translate~")."<br>";
    endif;
    $SOLVED_TEXT = str_replace("~translate~","",str_replace("~/translate~","",$SOLVED_TEXT));
    return $SOLVED_TEXT;
}

function cloakURL($SOLVED_TEXT){
    if(!$GLOBALS["_CONFIG"]["DontCloakURL"]):
         $i=0;
         while($i>-1){
              $p = strpos($SOLVED_TEXT,"index.php?",$i);
              if($p>0):
                   $p_br = strpos($SOLVED_TEXT," ",$p);
                   $part[0] = substr($SOLVED_TEXT,0,$p+strlen("index.php?"));
                   $part[1] = substr($SOLVED_TEXT,strlen($part[0]),$p_br-strlen($part[0]));
                   $part[2] = substr($SOLVED_TEXT,$p_br);
                   $part[1] = "imc=".CryptURL($part[1],true);
                   $SOLVED_TEXT = $part[0].$part[1].$part[2];
                   $i = $p_br;
              else: $i = -1; endif;
         }
    endif;
    return $SOLVED_TEXT;
}

function validateText($SOLVED_TEXT){
    if(strpos($SOLVED_TEXT,"</head>")>0 OR strpos($SOLVED_TEXT,"<body")>0):
        // Versuch Validitt zu verbessern

        $stags = Array("<script","<link","<style","<object");
        $etags = Array("</script>","</link>","</style>","</object>");

        if(strpos($SOLVED_TEXT,"</head>")>0): $anchor = "</head>"; else: $anchor = "<body"; endif;

        for($i=0;$i<count($stags);$i++){
             while(strpos($SOLVED_TEXT,$stags[$i],strpos($SOLVED_TEXT,$anchor))>strpos($SOLVED_TEXT,$anchor)){
                  unset($tmp);
                  $totallength = strlen($SOLVED_TEXT);
                  $bodypos = strpos($SOLVED_TEXT,$anchor);
                  $lastscriptstart = strpos($SOLVED_TEXT,$stags[$i],$bodypos);
                  $lastscriptend = strpos($SOLVED_TEXT,$etags[$i],$bodypos);
                  $found = false;
                  // $positions = getIgnores($SOLVED_TEXT);
                  for($x=0;$x<count($positions[0]);$x++){
                       if($positions[0][$x]<=$lastscriptstart AND $positions[1][$x]>=$lastscriptend): $found = true; endif;
                  }
                  if(!$found):
                      $tmp[0] = substr($SOLVED_TEXT,0,$bodypos);
                      $tmp[1] = substr($SOLVED_TEXT,$lastscriptstart,$lastscriptend-$lastscriptstart+strlen($etags[$i]));
                      $tmp[2] = substr($SOLVED_TEXT,$bodypos,$lastscriptstart-$bodypos);
                      $tmp[3] = substr($SOLVED_TEXT,$lastscriptend+strlen($etags[$i]));
                      $SOLVED_TEXT = implode($tmp);
                  endif;
             }
        }
    endif;
    if(strpos($SOLVED_TEXT,"</body>")>0):
        $stags = Array("<bottom_div","<bottom_script");
        $etags = Array("</bottom_div>","</bottom_script>");

        $anchor = "</body>";
        for($i=0;$i<count($stags);$i++){
             $lastscriptstart = -1;
             while(!strpos($SOLVED_TEXT,$stags[$i],$lastscriptstart+1)===false){
                  // echo "OUTPUT #".$i.": ".str_replace("<","&lt;",$SOLVED_TEXT)."<hr>";
                  unset($tmp);
                  $totallength = strlen($SOLVED_TEXT);
                  $bodypos = strpos($SOLVED_TEXT,$anchor);
                  $lastscriptstart = strpos($SOLVED_TEXT,$stags[$i],$lastscriptstart+1);
                  $lastscriptend = strpos($SOLVED_TEXT,$etags[$i],$lastscriptstart+1)+strlen($etags[$i]);
                  $found = false;
                  //$positions = getIgnores($SOLVED_TEXT);
                  for($x=0;$x<count($positions[0]);$x++){
                       if($positions[0][$x]<=$lastscriptstart AND $positions[1][$x]>=$lastscriptend): $found = true; endif;
                  }
                  if(!$found AND $bodypos>$lastscriptstart):
                  
                      //echo "Bodypos: ".$bodypos."<br>Lastscriptstart: ".$lastscriptstart."<br>Lastscriptend: ".$lastscriptend."<br>";
                      
                      // echo "<table border=\"1\"><tr><td>before: ".str_replace("<","&lt;",$SOLVED_TEXT)."</td></tr></table>";
                      $script = substr($SOLVED_TEXT,$lastscriptstart,$lastscriptend-$lastscriptstart);
                      $beforescript = substr($SOLVED_TEXT,0,$lastscriptstart);
                      $afterscript = substr($SOLVED_TEXT,$lastscriptend+strlen("</script>"));
                      
                      $withoutscript = $beforescript.$afterscript;
                      $insertpos = strpos($SOLVED_TEXT,$anchor)+strlen($anchor);
                      
                      $SOLVED_TEXT = injectText($withoutscript,str_replace("bottom_","bottomsolved_",$script),$anchor,1);
                      //echo "<table border=\"1\"><tr><td>after: ".str_replace("<","&lt;",$SOLVED_TEXT)."</td></tr></table>";
                  elseif(!$found):
                      $startbody = substr($SOLVED_TEXT,0,$lastscriptstart);
                      $script = substr($SOLVED_TEXT,$lastscriptstart,$lastscriptend-$lastscriptstart);
                      $endheader = substr($SOLVED_TEXT,$lastscriptend);
                      $SOLVED_TEXT = $startbody.str_replace("bottom_","bottomsolved_",$script).$endheader;
                  endif;
             }
        }
    endif;
    return $SOLVED_TEXT;
}

function getIgnores($SOLVED_TEXT){
    $weiter = true;
    $pos = -1;
    while($weiter){
         $pos = strpos($SOLVED_TEXT,"~ignore~",$pos+1);
         $pose = strpos($SOLVED_TEXT,"~/ignore~",$pos);
         if($pos>0 AND $pose>0):
              $positions[0][count($positions)] = $pos;
              $positions[1][count($positionse)] = $pose;
         else: $weiter = false;
         endif;
    }
    return $positions;
}

function injectText($source,$text,$anchor,$after){
    $insertpos = strpos($source,$anchor);
    if($after=="1"): $insertpos += strlen($anchor); endif; // ($after)?strlen($anchor):0;
    return substr($source,0,$insertpos).$text.substr($source,$insertpos);
}

?>