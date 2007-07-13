<?

define("imap_host","");
define("imap_port","143");
define("imap_user",$_SESSION["user"]);
define("imap_pw",$_SESSION["userpw"]);

$resources["todo"][] = "PIM/Aufgaben";
$resources["calendar"][] = "PIM/Kalender";
$resources["contact"][] = "PIM/Kontakte";

?>

