<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 * 
 * Handles the login form - the entry page
 */
class LayoutLogin extends Layout
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display the header
     * 
     * @return string
     */
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
                            <ul class="nav navbar-nav navbar-right">
                                <li class="active"><a href="/login/">Login <span class="sr-only">(current)</span></a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            ';
        return $return;
    }

    /**
     * Display the login page
     * 
     * @param string $error
     * @return type
     */
    public function getLoginPage($error = false)
    {
        $return = '
            <div class="row">

                <div class="col-md-6 col-md-offset-3 well">

                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <legend>Inloggen</legend>
                            ';
        if ($error)
        {
            if ($error == "incorrect")
            {
                $return .= '
                            <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>Uw inloggegevens zijn incorrect.</strong> Probeer het opnieuw.
                            </div>
                ';
            }
            else if ($error == "gone")
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
                                            <a href="/forgot-pass/" class="btn btn-link">Wachtwoord vergeten?</a>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-right">
                                                <button type="submit" name="submitLogin" class="btn btn-primary nonajax">Inloggen</button>
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

    /**
     * Displays the forgot password page
     * 
     * @param type $error
     * @param type $email
     * @return type
     */
    public function getForgotPasswordPage($error = false, $email = null)
    {
        $return = '
            <div class="row">

                <div class="col-md-6 col-md-offset-3 well">

                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <legend>Wachtwoord vergeten</legend>
                            ';
        if ($error)
        {
            if ($error == "incorrect")
            {
                $return .= '
                            <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                Dit emailadres komt niet voor in onze database.
                            </div>
                ';
            }
            else if ($error == "proceed")
            {
                if ($this->sendForgotPasswordMail($email))
                {
                    $return .= '
                            <div class="alert alert-dismissible alert-success">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                Er is een mail naar <strong>' . $email . '</strong> gestuurd met daarin een link om een nieuw wachtwoord te kunnen instellen.
                            </div>
                    ';
                }
                else
                {
                    $return .= '
                            <div class="alert alert-dismissible alert-danger">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                Er is een fout opgetreden bij het verzenden van de mail..
                            </div>
                    ';
                }
            }
        }
        $return .= '
                            <div class="form-group">
                                <label for="inputEmail" class="col-md-3 control-label">E-mailadres:</label>
                                <div class="col-md-9">
                                    <input type="text" name="email" class="form-control" id="inputEmail" placeholder="Email">
                                    <div class="checkbox">

                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <button type="submit" name="submitPasswordForgot" class="btn btn-primary nonajax">Verzenden</button>
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

    /**
     * Sends a mail to the user when he requests another password
     * 
     * @param type $email
     * @return boolean
     */
    private function sendForgotPasswordMail($email)
    {
        $cUser = new User();
        $userData = $cUser->getUserByEmail($email);

        $sql = "UPDATE USER
                SET Forgot_password_token = '" . time() . "'
                WHERE Email = " . $this->getEncryptValueString(":email");
        $result = $this->query($sql, array(
            ":email" => array($email, PDO::PARAM_STR)
        ));

        if ($result)
        {
            // phpmailer object
            $mail = new PHPMailer();
            $mail->CharSet = "UTF-8";

            //From email address and name
            $mail->From = EMAIL;
            $mail->FromName = "Sonja Aalbers";

            //To address and name
            $mail->addAddress($email, $userData->Name);
            //Address to which recipient will reply
            $mail->addReplyTo(EMAIL, "Sonja Aalbers");

            //Send HTML or Plain Text email
            $mail->isHTML(true);

            $mail->Subject = "Wachtwoord vergeten muziektherapie";
            $mail->Body = "
                <div style=\"padding: 40px 20px;\">
                    Geachte " . $userData->Name . ",<br /><br />

                    Op de website heeft u een verzoek tot het verkrijgen van een nieuw wachtwoord gedaan.<br />
                    Via onderstaande link kunt u een nieuw wachtwoord voor uw account opgeven:<br />
                    <a href='" . DOMAIN . "/renew-password/" . urlencode($email) . "/" . $userData->Forgot_password_token . "'>" . DOMAIN . "/renew-password/" . urlencode($email) . "/" . $userData->Forgot_password_token . "</a>

                    Met vriendelijke groet,<br /><br />

                    Sonja Aalbers - Muziektherapie<br />
                    <a href='" . DOMAIN . "'>" . DOMAIN . "</a>
                </div>
            ";
            $mail->AltBody = "U heeft een email programma nodig die HTML ondersteund.";

            if (!$mail->send())
            {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
            else
            {
                return true;
            }
        }
        return false;
    }

}
