<?

if(phPIMap!="ok") die("Direct access to this location is not allowed");

append("<html>
     <head>
          <title>phPIMap - Manage PIM data FROM IMAP</title>
          <link rel=\"stylesheet\" type=\"text/css\" href=\"front/sources/style.css\" media=\"screen\" />
          <script type=\"text/javascript\" src=\"front/sources/selfwritten.js\"></script>
          <script type=\"text/javascript\" src=\"front/sources/prototype.js\"></script>
          <script type=\"text/javascript\" src=\"front/sources/scriptaculous/scriptaculous.js\"></script>
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

include("back/modules/list.calendar.php");

append("</div>

                         <div id=\"listtodo\">");

include("back/modules/list.todo.php");

append("</div>

                         <div id=\"listcontact\">");

include("back/modules/list.contact.php");

append("</div>
               </div>
          <div id=\"details\" name=\"details\">");

switch(RetrieveVar("module","0110")){
     case "calendar":
          include("back/modules/detail.calendar.php");
          break;
     case "contact":
          include("back/modules/detail.contact.php");
          break;
     case "todo":
          include("back/modules/detail.todo.php");
          break;
     case "sync":
          include("back/modules/sync.php");
     break;
     default:
          include("back/modules/detail.calendar.php");
}

append("                 </div>
          </div>
     </body>
</html>");

?>