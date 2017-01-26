<?php

/**
 * @author: Reinier Gombert, Patrick Pieper
 * @date: 14-dec-2016
 */
require_once ($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

if (isset($_REQUEST['action']))
{
    $action = stripslashes(json_decode($_REQUEST['action']));

    if ($action === 'drawGraph')
    {
        $treatmentID = stripslashes(json_decode($_REQUEST["treatmentid"]));
        $role = stripslashes(json_decode($_REQUEST["role"]));

        if ($role == "therapist")
        {
            $roleName = "Therapeut";
        }
        else
        {
            $roleName = "Client";
        }
        
        $cTreatment = new Treatment();
        $aParams = $cTreatment->drawGraph($treatmentID, $roleName);
        
        if (!empty($aParams))
        {
            echo
            json_encode(
                    array(
                        "status" => "ok",
                        "result" => $aParams,
                    )
            );
        }
        else
        {
            echo
            json_encode(
                    array(
                        "status" => "error",
                        "message" => "Er zijn geen resultaten gevonden",
                    )
            );
        }
        //}
    }
}