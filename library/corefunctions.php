<?php

function utf8_strlen($s)
{
    return preg_match_all('/./u', $s, $tmp);
}
