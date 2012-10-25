<?php
    require_once('wptwitterhandler.php');
    session_start();
    $handler = new WPTwitterHandler();
    $handler->authorize();
?>