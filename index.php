<?php
// include global config file
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

if(isset($_GET["logout"]))
{
    unset($_SESSION["userID"]);
}

// get the page variables
$page = (isset($_GET["page"]) ? $_GET["page"] : null);
$subpage = (isset($_GET["subpage"]) ? $_GET["subpage"] : null);

// create new instance of Page class
$cPage = new Page($page, $subpage);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="css/muziektherapie.css">
        <link rel="stylesheet" type="text/css" href="css/footer.css">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <title>Muziektheraphie</title>
    </head>
    <body>
        <div class="container">

            <?php
            $cPage->display();
            ?>

        </div>
    </body>
</html>