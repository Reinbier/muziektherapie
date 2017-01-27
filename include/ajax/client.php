<?php

/**
 * @author: Reinier Gombert, Patrick Pieper
 * @date: 14-dec-2016
 */
require_once ($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

// if there is an action
if (isset($_REQUEST['action']))
{
    // get action
    $action = stripslashes(json_decode($_REQUEST['action']));

    // drawgraph
    if ($action === 'drawGraph')
    {
        // get vars
        $treatmentID = stripslashes(json_decode($_REQUEST["treatmentid"]));
        $role = stripslashes(json_decode($_REQUEST["role"]));

        // set rolename in dutch
        if ($role == "therapist")
        {
            $roleName = "Therapeut";
        }
        else
        {
            $roleName = "Client";
        }

        // get data for graph for this rolename
        $cTreatment = new Treatment();
        $aParams = $cTreatment->drawGraph($treatmentID, $roleName);

        // return parameters to the js file
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
    }
}