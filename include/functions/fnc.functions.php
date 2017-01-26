<?php

/**
 * @author: Reinier Gombert
 * @date: 20-jan-2017
 */

/**
 * Method for converting any kind of date and/or time to the Dutch format
 * 
 * @param float $input      containing the date and/or time
 * @return string           containing Dutch formatted date and/or time
 */
function NederlandseDatumTijd($input)
{
    // if it is numeric, it is already a timestring
    return date("d-m-Y H:i:s", (!is_numeric($input) ? strtotime($input) : $input));
}

function convertRolesToStringForQuery($roles)
{
    foreach ($roles as &$role)
    {
        $role = "'" . $role . "'";
    }
    $newRoles = implode(", ", $roles);

    return $newRoles;
}
