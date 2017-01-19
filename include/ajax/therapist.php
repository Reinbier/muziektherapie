<?php

/**
 * @author: Reinier Gombert
 * @date: 14-dec-2016
 */

// include global config file
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');


if (isset($_REQUEST["action"]))
{
    $action = stripslashes(json_decode($_REQUEST["action"]));
    
    if($action === "createTherapist")
    {
        $userParams = json_decode($_REQUEST["therapistParams"], true);
        $cUser = new User;
        
        $result = $cUser->insertUser($userParams, "Therapeut");
        
        echo json_encode(
                array(
                    "title" => "Succes!", 
                    "text" => "De therapeut is aangemaakt", 
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>",
                    "lastQuery" => $cUser->getLastQuery(),
                    "lastResult" => $cUser->getLastQueryResult(),
                    "lastMysqlError" => $cUser->getLastMysqlError()
                )
            );
    }
    else if($action === "createClient")
    {
        $userParams = json_decode($_REQUEST["clientParams"], true);
        $cUser = new User;
        
        $result = $cUser->insertUser($userParams, "Client");
        
        echo json_encode(
                array(
                    "title" => "Succes!", 
                    "text" => "De client is aangemaakt", 
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>",
                    "lastQuery" => $cUser->getLastQuery(),
                    "lastResult" => $cUser->getLastQueryResult(),
                    "lastMysqlError" => $cUser->getLastMysqlError()
                )
            );
    }
    else if ($action === 'drawGraph')
    {
        $userID = stripslashes(json_decode($_REQUEST["userid"]));
        $cTreatment = new Treatment();

        $aParams = $cTreatment->drawGraph($userID, "Therapeut");
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
else
{
    echo json_encode(
            array(
                "title" => "Aanvraag mislukt", 
                "text" => "Er zijn foutieve parameters opgegeven", 
                "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>"
            )
        );
}