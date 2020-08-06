<?php

function dd($debug)
{
    echo "<pre>";
    var_dump($debug);
    die;
    echo "</pre>";
}
