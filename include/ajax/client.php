<?php

/**
 * @author: Reinier Gombert, Patrick Pieper
 * @date: 14-dec-2016
 */

require_once ($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

if (isset($_REQUEST['action']))
{
	$userID = $_SESSION['userID'];
	$action = stripslashes(json_decode($_REQUEST['action']));

	if ($action === 'drawGraph')
	{
		$cTreatment = new Treatment();

		$aParams = $cTreatment->drawGraph($userID, "client");
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