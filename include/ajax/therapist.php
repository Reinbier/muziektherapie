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

    if ($action === "createTherapist")
    {
        $userParams = json_decode($_REQUEST["therapistParams"], true);
        $cUser = new User;

        $result = $cUser->insertUser($userParams, "Therapeut");

        echo json_encode(
                array(
                    "title" => "Succes!",
                    "text" => "De therapeut is aangemaakt",
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Sluiten</button>",
                    "lastQuery" => $cUser->getLastQuery(),
                    "lastResult" => $cUser->getLastQueryResult(),
                    "lastMysqlError" => $cUser->getLastMysqlError()
                )
        );
    }
    else if ($action === "createClient")
    {
        $userParams = json_decode($_REQUEST["clientParams"], true);
        $cUser = new User;

        $result = $cUser->insertUser($userParams, "Client");

        echo json_encode(
                array(
                    "title" => "Succes!",
                    "text" => "De client is aangemaakt",
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Sluiten</button>",
                    "lastQuery" => $cUser->getLastQuery(),
                    "lastResult" => $cUser->getLastQueryResult(),
                    "lastMysqlError" => $cUser->getLastMysqlError()
                )
        );
    }
    else if ($action === "createKin")
    {
        $userParams = json_decode($_REQUEST["kinParams"], true);
        $roleName = stripslashes(json_decode($_REQUEST["roleName"]));
        $cUser = new User;

        $result = $cUser->insertUser($userParams, $roleName);

        echo json_encode(
                array(
                    "title" => "Succes!",
                    "text" => "De " . lcfirst($roleName) . " is aangemaakt",
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Sluiten</button>",
                    "lastQuery" => $cUser->getLastQuery(),
                    "lastResult" => $cUser->getLastQueryResult(),
                    "lastMysqlError" => $cUser->getLastMysqlError()
                )
        );
    }
    else if ($action === "addKinToTreatment")
    {
        $kinID = stripslashes(json_decode($_REQUEST["kinID"]));
        $treatmentID = stripslashes(json_decode($_REQUEST["treatmentID"]));

        $cTreatment = new Treatment();
        // carry out insert query
        $cTreatment->addUserToTreatment($kinID, $treatmentID);

        echo json_encode(
                array(
                    "title" => "Succes!",
                    "text" => "De naaste is toegevoegd aan deze behandeling",
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal' data-reload='yes'>Sluiten</button>",
                    "lastQuery" => $cTreatment->getLastQuery(),
                    "lastResult" => $cTreatment->getLastQueryResult(),
                    "lastMysqlError" => $cTreatment->getLastMysqlError(),
                    "reload" => true
                )
        );
    }
    else if ($action === "createTreatment")
    {
        $name = stripslashes(json_decode($_REQUEST["name"]));
        $client = stripslashes(json_decode($_REQUEST["client"]));
        $therapist = stripslashes(json_decode($_REQUEST["therapist"]));

        $cTreatment = new Treatment();
        // insert the treatment
        $treatmentID = $cTreatment->createTreatment($name);
        // add client as well as the therapist to this treatment
        $cTreatment->addUserToTreatment($client, $treatmentID);
        $cTreatment->addUserToTreatment($therapist, $treatmentID);

        echo json_encode(
                array(
                    "title" => "Succes!",
                    "text" => "De behandeling is aangemaakt!",
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal' data-reload='yes'>Sluiten</button>",
                    "lastQuery" => $cTreatment->getLastQuery(),
                    "lastResult" => $cTreatment->getLastQueryResult(),
                    "lastMysqlError" => $cTreatment->getLastMysqlError(),
                    "reload" => true
                )
        );
    }
    else if ($action === "actionTreatment")
    {
        $todo = $_REQUEST["todo"];
        $treatmentID = $_REQUEST["treatmentID"];

        $cTreatment = new Treatment();
        // what to do
        if ($todo == "finish")
        {
            $cTreatment->finishTreatment($treatmentID);
            $text = "De behandeling is afgerond!";
        }
        else if ($todo == "delete")
        {
            $cTreatment->deleteTreatment($treatmentID);
            $text = "De behandeling is verwijderd!";
        }

        echo json_encode(
                array(
                    "title" => "Succes!",
                    "text" => $text,
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal' data-reload='yes'>Sluiten</button>",
                    "lastQuery" => $cTreatment->getLastQuery(),
                    "lastResult" => $cTreatment->getLastQueryResult(),
                    "lastMysqlError" => $cTreatment->getLastMysqlError(),
                    "reload" => true
                )
        );
    }
    else if ($action === "createMeasurement")
    {
        $name = stripslashes(json_decode($_REQUEST["name"]));
        $questionlistID = stripslashes(json_decode($_REQUEST["questionlistID"]));
        $treatmentID = stripslashes(json_decode($_REQUEST["treatmentID"]));

        if (is_numeric($questionlistID) && is_numeric($treatmentID))
        {
            $cMeasurement = new Measurement();
            // insert the measurement
            $measurementID = $cMeasurement->createMeasurement($name, $treatmentID, $questionlistID);

            echo json_encode(
                    array(
                        "title" => "Succes!",
                        "text" => "De meting is toegevoegd aan de behandeling!",
                        "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal' data-reload='yes'>Sluiten</button>",
                        "lastQuery" => $cMeasurement->getLastQuery(),
                        "lastResult" => $cMeasurement->getLastQueryResult(),
                        "lastMysqlError" => $cMeasurement->getLastMysqlError(),
                        "reload" => true
                    )
            );
        }
        else
        {
            $note = "Probeer opnieuw via het <a href='/overzicht/behandelingen/'>overzicht</a> een behandeling te selecteren en een meting te starten.";
            if (is_numeric($treatmentID))
            {
                $note = "Probeer opnieuw een meting te starten via het <a href='/overzicht/behandelingen/" . $treatmentID . "/'>vorige scherm</a>.";
            }
            echo json_encode(
                    array(
                        "title" => "Er is iets fout gegaan..",
                        "text" => "De meting kon niet toegevoegd worden aan de behandeling. " . $note,
                        "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Sluiten</button>"
                    )
            );
        }
    }
    else if ($action === 'drawGraph')
    {
        $treatmentID = stripslashes(json_decode($_REQUEST["treatmentid"]));
        $role = stripslashes(json_decode($_REQUEST["role"]));
        
        if($role == "therapist")
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
    }
}
else
{
    echo json_encode(
            array(
                "title" => "Aanvraag mislukt",
                "text" => "Er zijn foutieve parameters opgegeven",
                "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Sluiten</button>"
            )
    );
}