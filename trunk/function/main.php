<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

append("<html>
     <head>
          <title>phPIMap - Manage PIM data FROM IMAP</title>
          <link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\" media=\"screen\" />
          <script type=\"text/javascript\" src=\"js/selfwritten.js\"></script>
          <script type=\"text/javascript\" src=\"thirdparty/prototype.js\"></script>
          <script type=\"text/javascript\" src=\"thirdparty/scriptaculous/scriptaculous.js\"></script>
     </head>
     <body>
          <div id=\"main\">
               <div id=\"menu\" name=\"menu\">
                    <div><a href=\"?module=sync\">Sync</a></div>
                    <div><a href=\"?module=calendar\">Calendar</a></div>
                    <div><a href=\"?module=contact\">Contacts</a></div>
                    <div><a href=\"?module=todo\">Todo</a></div>
                    <div id=\"messenger\" name=\"messenger\">&nbsp;</div>
               </div>
               <div id=\"sidebar\">
                         <div id=\"listcalendar\">");

include("functions/modules/list.calendar.php");

append("</div>

                         <div id=\"listtodo\">");

include("functions/modules/list.todo.php");

append("</div>

                         <div id=\"listcontact\">");

include("functions/modules/list.contact.php");

append("</div>
               </div>
          <div id=\"details\" name=\"details\">");

switch(RetrieveVar("module","0110")){
     case "calendar":
          include("functions/modules/detail.calendar.php");
          break;
     case "contact":
          include("functions/modules/detail.contact.php");
          break;
     case "todo":
          include("functions/modules/detail.todo.php");
          break;
     case "sync":
          include("functions/modules/sync.php");
     break;
     default:
          include("functions/modules/detail.calendar.php");
}

append("                 </div>
          </div>
     </body>
</html>");

?>