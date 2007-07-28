<?

$tpl = file_get_contents(designroot."/display/form.settings.html");
$tpl = str_replace("~imap_host~",imap_host,$tpl);
$tpl = str_replace("~imap_port~",imap_port,$tpl);
$tpl = str_replace("~imap_user~",imap_user,$tpl);
$tpl = str_replace("~imap_pw~",imap_pw,$tpl);
$tpl = str_replace("~phpimap_pw~",phpimap_pw,$tpl);

$tpl = str_replace("~imap_todo~",implode(" ",$GLOBALS["resources"]["todo"]),$tpl);
$tpl = str_replace("~imap_calendar~",implode(" ",$GLOBALS["resources"]["calendar"]),$tpl);
$tpl = str_replace("~imap_contact~",implode(" ",$GLOBALS["resources"]["contact"]),$tpl);

append($tpl);

?>