<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title></title>
</head>
<body style="background-color: #ffffff; color: #000000; font-style: normal; font-variant: normal; font-weight: normal; font-size: 12px; line-height: 18px; font-family: helvetica, arial, verdana, sans-serif;">
<h2 style="background-color: #eeeeee;">Посланы новые данные</h2><table cellspacing="0" cellpadding="0" width="100%" style="background-color: #ffffff;">
<tr><td valign="top" style="background-color: #ffffff;"><b>Имя:</b></td><td><?=htmlentities($_REQUEST["firstname"],ENT_COMPAT,'UTF-8')?></td></tr>
<tr><td valign="top" style="background-color: #ffffff;"><b>Фамилия:</b></td><td><?=htmlentities($_REQUEST["lastname"],ENT_COMPAT,'UTF-8')?></td></tr>
<tr><td valign="top" style="background-color: #ffffff;"><b>Телефон:</b></td><td><?=htmlentities($_REQUEST["phone_number"],ENT_COMPAT,'UTF-8')?></td></tr>
<tr><td valign="top" style="background-color: #ffffff;"><b>E-mail:</b></td><td><?=htmlentities($_REQUEST["email"],ENT_COMPAT,'UTF-8')?></td></tr>
<tr><td valign="top" style="background-color: #ffffff;"><b>Full URL:</b></td><td><?=htmlentities($_REQUEST["full_url"],ENT_COMPAT,'UTF-8')?></td></tr>
</table><br/><br/>
    <div style="background-color: #eeeeee; font-size: 10px; line-height: 11px;">Форма прислана с сайта: <?=htmlentities($_SERVER["SERVER_NAME"],ENT_COMPAT,'UTF-8')?></div>
    <div style="background-color: #eeeeee; font-size: 10px; line-height: 11px;">Visitor IP address: <?=htmlentities($_SERVER["REMOTE_ADDR"],ENT_COMPAT,'UTF-8')?></div>
    </body>
</html>