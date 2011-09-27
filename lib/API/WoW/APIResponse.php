<?php

interface ApiResponse
{
    function getData($asArray = false);

    function getTTL();
}

?>