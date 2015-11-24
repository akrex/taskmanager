<?php
// ONLY FOR CRON TASKS
// Данный файл предназначен для рассилски уведомлений
// о тасках на email и телефоны
// Желательно запускать раз в 5 минут
include_once("../config.php");
//защита от браузеров, запуск только с консоли!
header( 'Location: '.SITE_URL, true, 303 );

require_once("../library/database.php");

$db = new DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

function SendEmail($task){
    $subject = "Уведомление о событии";
    $message = ' 
    <html> 
        <head> 
            <title>Уведомление о событии '.$task["name"].'</title> 
        </head> 
        <body>
        <h2>Уважаемый '.$task["username"].'</h2>
        <p>Уведомляем вас о грядущем событии</p>
        <table>
        <tr><td>Название</td><td>Описание</td><td>Начало</td><td>Окончание</td></tr>
            <tr>
            <td>'.$task["name"].'</td>
            <td>'.$task["description"].'</td>
            <td>'.$task["start"].'</td>
            <td>'.$task["end"].'</td>
            </tr>
        </table>
            <div>
            С уважением администрация проекта '.SITE_NAME.'
            </div>
        </body> 
    </html>'; 

    $headers  = "Content-type: text/html; charset=UTF-8 \r\n"; 
    $headers .= "From: ".SITE_NAME." <".MAIL_FROM.">\r\n"; 

    mail($task["email"], $subject, $message, $headers);
    return true;
}

function SendSms($task){
    //заглушка для sms, настраиваеться под библиотеку
    return true;
}

function GetTaskList($db){
    $date = new DateTime();
    $sql = "SELECT u.*, t.*, ttu.* FROM `task` t, `task_to_user` ttu, `user` u WHERE "
            . "t.task_id = ttu.task_id AND u.user_id = ttu.user_id AND "
            . "((ttu.email_notify = 1 AND ttu.email_sended = 0 AND u.email_checked = 1) OR "
            . "(ttu.phone_notify = 1 AND ttu.phone_sended = 0)) AND ";
    $sql .= "t.end > '".$date->format('Y-m-d H:i:s')."' AND ";
    $date->add(new DateInterval("PT60M"));
    $sql .= "t.start <= '".$date->format('Y-m-d H:i:s')."'";
    return $db->query($sql)->rows;
}

function SaveState($completed, $db){
    foreach($completed["email"] as $item){
        $sql = "UPDATE `task_to_user` SET email_sended = 1 "
                . "WHERE task_id = '".$item["task_id"]."' AND user_id = '".$item["user_id"]."'; ";
        $db->query($sql);
    }
    foreach($completed["phone"] as $item){
        $sql = "UPDATE `task_to_user` SET phone_sended = 1 "
                . "WHERE task_id = '".$item["task_id"]."' AND user_id = '".$item["user_id"]."'; ";
        $db->query($sql);
    }
}


//получаем список тех, кому нужно сообщить о таске
$task_list = GetTaskList($db);
//отправляем
$completed = array(
    "email" => array(), 
    "phone" => array()
);
foreach($task_list as $item){
    //если нужно отправить email
    if($item["email_notify"] == 1){
        if(SendEmail($item)){
            //добавить в $completed
            array_push($completed["email"], $item);
        }
    }
    if($item["phone_notify"] == 1){
        if(SendSms($task)){
            //добавить в $completed
            array_push($completed["phone"], $item);
        }
    }
}
//Saving send state
SaveState($completed, $db);
