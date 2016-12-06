<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */
class LayoutLogin extends Layout
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getHeader()
    {
        $return = parent::getHeader();

        $return .= '
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
                                <li><a href="/home/">Home</a></li>
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="active"><a href="/login/">Login <span class="sr-only">(current)</span></a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            ';
        return $return;
    }

    public function getLoginPage($error = false)
    {
        $return = '
            <div class="row">

                <div class="col-md-6 col-md-offset-3 well">

                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <legend>Inloggen</legend>
                            ';
        if($error)
        {
            if($error == "incorrect")
            {
                $return .= '
                            <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Uw inloggegevens zijn incorrect.</strong> Probeer het opnieuw.
                            </div>
                ';
            }
            else if($error == "gone")
            {
                $return .= '
                            <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Niet ingelogd.</strong> Login om door te gaan.
                            </div>
                ';
            }
        }
        $return .= '
                            <div class="form-group">
                                <label for="inputEmail" class="col-md-3 control-label">E-mailadres:</label>
                                <div class="col-md-9">
                                    <input type="text" name="email" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword" class="col-md-3 control-label">Wachtwoord:</label>
                                <div class="col-md-9">
                                    <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                                    <div class="checkbox">

                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <button type="reset" class="btn btn-link">Wachtwoord vergeten?</button>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-right">
                                                <button type="submit" name="submitLogin" class="btn btn-primary">Inloggen</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>

                </div>

            </div>
        ';
        
        return $this->getHeader() . parent::getContent($return) . $this->getFooter();
    }

}
