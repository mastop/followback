<?php
if ($locale == "pt_BR" || $_COOKIE['LANG'] == "PT") {
    include("language/pt-br/footer.html");
}else{
    include("language/eng/footer.html");
}