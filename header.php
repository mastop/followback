<?php
$locale = Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
if (isset($_COOKIE["LANG"])) {
    if ($_COOKIE["LANG"] == "PT") {
        include("language/pt-br/pt_br.php");
        $date = "d/m/Y H:i";
    } else {
        include("language/eng/eng.php");
        $date = "m/d/Y H:i";
    }
} elseif ($locale == "pt_BR") {
    include("language/pt-br/pt_br.php");
    $date = "d/m/Y H:i";
} else {
    include("language/eng/eng.php");
    $date = "m/d/Y H:i";
}
include ("layout/header.html");