<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="css/muziektherapie.css">
    <link rel="stylesheet" type="text/css" href="css/footer.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <title>Muziektheraphie</title>
    <script>
        $(function () {
            $("input[type='radio']").checkboxradio();
        });
    </script>
</head>
<body>
    <div class="container">

        <img class="img-responsive" src="images/Weblogo.png" alt="Sonja Aalbers" />

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"></a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="therapeutHome.html">Admin home <span class="sr-only">(current)</span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Client <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="grafiek.html">Weergeven</a></li>
                                <li><a href="AanmakenCliënt.html">Aanmaken</a></li>
                                <li><a href="#">Bewerken</a></li>
                                <li><a href="#">Verwijderen</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Therapeut <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Aanmaken</a></li>
                                <li><a href="#">Bewerken</a></li>
                                <li><a href="#">Verwijderen</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Naaste <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Aanmaken</a></li>
                                <li><a href="#">Bewerken</a></li>
                                <li><a href="#">Verwijderen</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Vragenlijst <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="AanmakenVragenlijst.html">Aanmaken</a></li>
                                <li><a href="#">Bewerken</a></li>
                                <li><a href="#">Verwijderen</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="navbar-text">Ingelogd als Sonja</li>
                        <li><a href="index.html">Uitloggen <span class="sr-only">(current)</span></a></li>
                    </ul>
                </div>
            </div>
        </nav>
<div class="container-fluid">
                <div class="row">
                    
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h3 class="panel-title">Meteen:</h3>
                            </div>
                            <div class="panel-body">
                              <a href="#" class="btn btn-success">Sessie starten</a>
                            </div>
                          </div>
                    </div>
                    
                    <div class="col-md-6 col-md-offset-1 well">
                        <h3>Meldingen</h3>
                        <p>Op dit moment geen meldingen om weer te geven</p>
                    </div>
                    
                </div>
                        
</div>
<footer class="bd-footer text-muted">
    <div class="container">
        <ul class="bd-footer-links">
            <li><a href="https://facebook.com">Facebook</a></li>
            <li><a href="https://twitter.com/">Twitter</a></li>
            <li><a href="https://plus.google.com/">Google+</a></li>
        </ul>
        <p>Project Muziektherapie</p>
        <p>Copyright 2016. All rights reserved.</p>
    </div>
</footer>

</div>
</body>
</html>
