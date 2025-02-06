<?php

if (!function_exists('persen')) {
    function persen($persen, $price)
    {
        return ($persen / 100) * $price;
    }
}
