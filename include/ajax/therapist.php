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
    $userParams = stripslashes(json_decode($_REQUEST["therapistParams"]));
    
    if($action === "create")
    {
        $cUser = new User;
        
        $result = $cUser->insertUser($userParams, "Therapeut");
        
        echo json_encode(
                array(
                    "title" => "Succes!", 
                    "text" => "De therapeut is aangemaakt", 
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>"
                )
            );
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