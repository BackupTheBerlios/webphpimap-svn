<?

################################################
## Robert Schrenk, 2007, phPIMap              ##
## This code is distributed under the GNU/GPL ##
################################################

if($_POST["settings_form"]!=""){
     $settings = "<?\n\n";
     $settings .= "################################################\n";
     $settings .= "## Robert Schrenk, 2007, phPIMap              ##\n";
     $settings .= "## This code is distributed under the GNU/GPL ##\n";
     $settings .= "################################################\n\n";
     
     $receivables = Array("imap_host","imap_port","imap_user","imap_pw","phpimap_pw");
     for($i=0;$i<count($receivables);$i++){
          if($_POST["settings_form"]=="1") // Receive new settings
               $settings .= "define(\"".$receivables[$i]."\",\"".$_POST[$receivables[$i]]."\");\n";
           else // Keep actual settings
               $settings .= "define(\"".$receivables[$i]."\",\"".constant($receivables[$i])."\");\n";
     }
     $settings .= "\n";
     
     $resources = Array("todo","calendar","contact");
     for($i=0;$i<count($resources);$i++){
          if($_POST["settings_form"]=="2") { // Receive new Folders
	          $x = explode(" ",$_POST["imap_".$resources[$i]]);
	          for($y=0;$y<count($x);$y++)
	               $settings .= "\$resources[\"".$resources[$i]."\"][] = \"".$x[$y]."\";\n";
	      } else { // Keep actual folders
	          for($y=0;$y<count($GLOBALS["resources"][$resources[$i]]);$y++)
	               $settings .= "\$resources[\"".$resources[$i]."\"][] = \"".$GLOBALS["resources"][$resources[$i]][$y]."\";\n";
	      }
     }
     
     $settings .= "\n\n?>";
     
     file_put_contents(userdir."/".$_SESSION["user"]."/settings.php",$settings);
     Header("Location: ".domain."?module=".$_GET["module"]."&open=".$_GET["open"]);
}


switch($State){
     case "app":
          include("list.settings.php");
     break;
     default:
          include("form.settings.php");

}


?>