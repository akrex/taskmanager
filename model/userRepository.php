<?php

class UserRepository extends Model{
    public function AddUser($data){
        $date = new DateTime();
        $token = md5($date+$data["username"]);
        $sql = "INSERT INTO `user`(Username,FIO,Email,password,phone,mail_verefication_token)"
                . "VALUES('".$this->db->escape($data["username"]).
                "','".$this->db->escape($data["FIO"])."','".
                $this->db->escape($data["email"])."','".
                md5($data["password"])."','".
                $this->db->escape($data["phone"])."','".
                $token."');";
        $result = $this->db->query($sql);
        //отправляем email
        $body = "Поздравляем вас с регистрацией на <a href='".SITE_URL."'>".SITE_NAME."</a></br>";
        $body .= "Для того, что бы полностью пользываться всеми услугами нашего сайта подтвердите свой email ";
        $body .= "<a href='".SITE_URL."?controller=profile&action=vereficationEmail&key=".$token."'>перейя по ссылке</a>";
        $this->SendEmail($data["email"], "Подтверждение email", $body);
    }
    
    public function GetUserById($user_id){
        $sql = "SELECT * FROM `user` WHERE user_id=\"".$user_id."\"";
        return $this->db->query($sql)->row;
    }
    
    public function UpdateUser($user_id,$data){
        $user = $this->GetUserById($user_id);
        $sql = "UPDATE `user` SET ";
        if($data["password"]!=""){
            $sql .= "password=\"".md5($data["password"])."\", ";
        }
        $sql .= "FIO=\"".$this->db->escape($data["FIO"])."\", ";
        $sql .= "email=\"".$this->db->escape($data["email"])."\", ";
        $sql .= "phone=\"".$this->db->escape($data["phone"])."\" ";
        if($user["email"] != $data["email"]){
            $date = new DateTime();
            $token = md5($date+$data["username"]);
            $sql .= ", mail_verefication_token=\"".$token."\", ";
            $sql .= "email_checked=0 ";
        }
        $sql .= "WHERE user_id=".$user_id;
        $result = $this->db->query($sql);
        
        //отправляем email подтвеждения, если email разные
        if($user["email"] != $data["email"]){
            $body = "Ваш email был изменён на сайте <a href='".SITE_URL."'>".SITE_NAME."</a></br>";
            $body .= "Для того, что бы полностью пользываться всеми услугами нашего сайта подтвердите свой email ";
            $body .= "<a href='".SITE_URL."?controller=profile&action=vereficationEmail&key=".$token."'>перейя по ссылке</a>";
            $this->SendEmail($data["email"], "Подтверждение email", $body);
        }
    }
    
    public function VereficateUser($token){
        $sql = "SELECT user_id FROM `user` WHERE mail_verefication_token = '".$this->db->escape($token)."';";
        $result = $this->db->query($sql);
        if($result->num_rows != 0){
            $sql = "UPDATE `user` SET email_checked=1, mail_verefication_token = \"\" "
                    . "WHERE user_id = '".$result->row["user_id"]."';";
            $this->db->query($sql);
            return true;
        }
        return false;
    }

    public function restoreByEmail($email){
        $sql = "SELECT * FROM `user` WHERE email='".$this->db->escape($email)."';";
        $result = $this->db->query($sql)->row;
        $date = new DateTime();
        $token = md5($date+$result["username"]+"rest");
        $sql = "UPDATE `user` SET restore_token='".$token."' WHERE user_id='".$result["user_id"]."';";
        $this->db->query($sql);
        //отправляем email для востановления пароля
        $body = "Уважаемый ".$result["username"]."!</br>";
        $body .= "Вы создали запрос на востановление пароля на сайте <a href='".SITE_URL."'>".SITE_NAME."</a></br>";
        $body .= "Для того что бы востановить пароль перейдите по сылки и следуйте инструкциям  ";
        $body .= "<a href='".SITE_URL."?controller=profile&action=restorestep2&key=".$token."'>Востановить пароль</a></br>";
        $body .= "Если вы не отпраляли запрос на востановление, то проигнорируйте это письмо!";
        $this->SendEmail($data["email"], "Востановление пароля", $body);
    }

    public function isRestoreTokenExist($token){
        $sql = "SELECT * FROM `user` WHERE restore_token='".$this->db->escape($token)."';";
        $result = $this->db->query($sql);
        return $result->num_rows != 0;
    }
    
    public function updatePasswordByToken($token,$password){
        $sql = "SELECT * FROM `user` WHERE restore_token='".$this->db->escape($token)."';";
        $result = $this->db->query($sql);
        $sql = "UPDATE `user` SET restore_token='', password='".md5($password)."' WHERE user_id='".$result->row["user_id"]."'";
        $this->db->query($sql);
    }
    
    public function IsUniqueUsername($username){
        $sql = "SELECT * FROM `user` WHERE `username`=\"".
                $this->db->escape($username).'"';
        $result = $this->db->query($sql);
        return $result->num_rows != 0;
    }
    
    public function IsUniqueEmail($email){
        $sql = "SELECT * FROM `user` WHERE `email`=\"".
                $this->db->escape($email).'"';
        $result = $this->db->query($sql);
        return $result->num_rows != 0;
    }
    
    private function SendEmail($to, $subject, $body){

        $message = ' 
        <html> 
            <head> 
                <title>'.$subject.'</title> 
            </head> 
            <body>'.$body.'
                <div>
                С уважением администрация проекта '.SITE_NAME.'
                </div>
            </body> 
        </html>'; 

        $headers  = "Content-type: text/html; charset=UTF-8 \r\n"; 
        $headers .= "From: ".SITE_NAME." <".MAIL_FROM.">\r\n"; 

        mail($to, $subject, $message, $headers); 
    }
}
