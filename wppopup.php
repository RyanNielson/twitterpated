<?php
    require_once('twitterhandler.php');
    session_start();
    $handler = new TwitterHandler();
    $handler->authorize();
?>