<?php
// include global config file
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

// logout user
if (isset($_GET["logout"]))
{
    unset($_SESSION["userID"]);
}

// get the page variables
$page = (isset($_GET["page"]) ? $_GET["page"] : null);
$subpage = (isset($_GET["subpage"]) ? $_GET["subpage"] : null);
$subsubpage = (isset($_GET["subsubpage"]) ? $_GET["subsubpage"] : null);

// create new instance of Page class
$cPage = new Page($page, $subpage, $subsubpage);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="/bootstrap/css/datetimepicker.min.css"/>
        <link rel="stylesheet" type="text/css" href="/css/muziektherapie.css">
        <link rel="stylesheet" type="text/css" href="/css/footer.css">
        <link rel="stylesheet" type="text/css" href="/css/morris.css">
        <link rel="stylesheet" type="text/css" href="/js/datatables/css/dataTables.bootstrap.min.css">
        <script src="/js/jquery.js"></script>
        <script src="/js/raphael.min.js"></script>
        <script src="/js/morris.min.js"></script>
        <script src="/js/moment.js"></script>
        <script src="/js/main.js"></script>
        <script src="/js/questionlist.js"></script>
        <script src="/bootstrap/js/datetimepicker.min.js"></script>
        <script src="/js/datatables/js/jquery.dataTables.min.js"></script>
        <script src="/js/datatables/js/dataTables.bootstrap.min.js"></script>
        <script src="/bootstrap/js/bootstrap.min.js"></script>
        <title>Muziektheraphie</title>
    </head>
    <body>
        <div class="container">

            <?php
            $cPage->display(); // display layout for current page
            ?>

        </div>
        <!-- include the HTML for the modal for further use via JavaScript -->
        <div class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <p>One fine body…</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>