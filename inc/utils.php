<?php

#Redirect function if user logged in, default to index.php
function redirect($url = "")
{
    echo
    "<script>
        window.location.href='" . $url . "';
    </script>";
}

function verifyCategory($getCategoryId, $categoryId)
{
    return $getCategoryId === $categoryId;
}

#Handle status to return it to javascript fetch
function throwStatusMessage($status)
{
    $jsonStatus = json_encode($status);
    echo $jsonStatus;
}