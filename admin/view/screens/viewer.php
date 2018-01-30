<?php
@require_once("../../../include/functions.php");
responseMethod();

function getContent( $parameters ) {
    $url = PROFILE::getURLAccess($parameters["id"]);
    if (is_null($url)):
        return "ACCESS_DENIED";
    endif;
    ob_start();
    @include_once("./{$url["DS_URL"]}/index.php");
    $out = ob_get_contents();
    ob_end_clean();
    return $out;
}
?>
