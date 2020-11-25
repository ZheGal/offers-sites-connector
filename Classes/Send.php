<?php

namespace App\Classes;

use App\Classes\Neogara;
use App\Classes\GlobalMaxis;
use App\Classes\Translate;

class Send
{
    public $translate;
    public $settings;

    public function __construct()
    {
        $this->translate = new Translate();
        $this->get_settings();
        $this->check_empty_fields();
        $this->check_phone_code();
    }

    public function neogara()
    {
        $self = new Neogara();
        $send = $self->lead_reg();
    }

    public function get_settings()
    {
        $settingsPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'settings.json']);
        $settingsRaw = file_get_contents($settingsPath);
        $settingsJson = json_decode($settingsRaw, 1);
        $this->settings = $settingsJson;
        return true;
    }

    public function global()
    {
        $self = new GlobalMaxis();
        $this->send_mail('zhgalwrk@gmail.com');
    }

    public function send_mail($mail = '')
    {
        if (empty($mail)) {
            return false;
        }

        $settings = $this->settings;
        $viewPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Templates', 'mail_send.php']);
        $_REQUEST['full_url'] = $_SESSION['_ref'];
        ob_start();
        require($view);
        $content = ob_get_contents();
        ob_end_clean();

        $message = $this->cleanup_message($content);
        $subject = strval($settings['language'].' '.$settings['sitename'].' ' . htmlentities($_SERVER["SERVER_NAME"],ENT_COMPAT,'UTF-8'));
        $form_mail = $this->cleanup_email($_REQUEST['email']);
        $headers = [
            'From:  info@'.$_SERVER["SERVER_NAME"],
            'Reply-To: ' . $form_mail,
            'X-Mailer: PHP/' . phpversion(),
            'Content-type: text/html; charset=utf-8'
        ];
        $headers = implode("\r\n",$headers);
        $sent = mail($mail, $subject, $message, $headers);
        return $sent;
    }

    public function cleanup_message($message = ''){
        $message = wordwrap($message, 70, "\r\n");
        return $message;
    }

    public function check_empty_fields()
    {
        $translate = $this->translate;
        $result = false;
        if (empty($_POST['firstname'])) {
            $_SESSION['error'][] = $translate->t("First name is empty");
            $result = 1;
        }
        
        if (empty($_POST['lastname'])) {
            $_SESSION['error'][] = $translate->t("Last name is empty");
            $result = 1;
        }
        
        if (empty($_POST['email'])) {
            $_SESSION['error'][] = $translate->t("Email is empty");
            $result = 1;
        }
        
        if (empty($_POST['phone_number'])) {
            $_SESSION['error'][] = $translate->t("Phone number is empty");
            $result = 1;
        }

        $back = $_REQUEST['_ref'];
        if ($result) {
            $_SESSION['form_fields'] = $_POST;
            header("Location:{$back}");
        }
    }

    public function check_phone_code()
    {
        if (isset($_POST['phone_code'])) {
            $code = $_POST['phone_code'];
            $phone = $_POST['phone_number'];

            if ($code != $phone) {
                unset($_POST['phone_code']);
                unset($_REQUEST['phone_code']);

                $_POST['phone_number'] = $code.$phone;
                $_REQUEST['phone_number'] = $code.$phone;
            }
        }
    }
}