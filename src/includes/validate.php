<?php

Class Planaday_validate
{

    function email($var) {
        $emailchk = strtolower(trim($var));
        if (preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/", $var)) {
            return true;
        }
        return false;
    }

    function postcode($var) {
        if (preg_match("/[0-9]{4}[ ]?[a-z]{2}/i", trim($var))) {
            return true;
        }
        return false;
    }

    function telefoonnummer($var) {
        if (preg_match("/[0-9]{2}-[0-9]{8}/",$var)
            OR preg_match("/[0-9]{2}[0-9]{8}/",$var)
            OR preg_match("/[0-9]{3}-[0-9]{7}/",$var)
            OR preg_match("/[0-9]{3}[0-9]{7}/",$var)
            OR preg_match("/[0-9]{4}[0-9]{6}/",$var)
            OR preg_match("/[0-9]{4}-[0-9]{6}/",$var)) {
            return true;
        }
        return false;
    }

}    