<?php
//клас отвечает за идентефикацию пользователя в системе
class UserIdentify{
    private $userToken;
    protected $registry;
    private $user;
    
    public function __get($key) {
            return $this->registry->get($key);
    }

    public function __set($key, $value) {
            $this->registry->set($key, $value);
    }
    
    public function __construct($registry) {
        $this->registry = $registry;
        if(isset($this->request->cookie["_uid"])){
            $this->userToken = $this->request->cookie["_uid"];
        }
        $this->checkToken($this->userToken);
    }
    
    public function login($username, $password){
        //ищем пользователя
        $result = $this->db->query("SELECT * FROM `user` WHERE username = \"".$this->db->escape($username).'";');
        //если не найден то не входим
        if($result->rows == 0){
            return false;
        }
        //если пароль не совпадает - то не входим
        if($result->row["password"] != md5($password)){
            return false;
        }
        //генерируем токен в системе
        $token = md5(time()+$username);
        //конец действия токена
        $nextmonth = time() + (30 * 24 * 60 * 60);
        //пишем его в базу
        $this->db->query("INSERT INTO `token`(user_id,token,data_expired)"
                . "VALUES(\"".$result->row["user_id"]."\",\"".$token."\",\"".
                date("Y-m-d H:i:s",$nextmonth)."\")");
        setcookie("_uid",$token,$nextmonth);
        return true;
    }
    //проверк токена.
    private function checkToken($token){
        $result = $this->db->query("SELECT `user`.*, `token`.token_id, `token`.token,"
                . "UNIX_TIMESTAMP(`token`.data_expired) as data_expired FROM `user`, `token`"
                . "WHERE `user`.user_id = `token`.user_id && `token`.token =\"".
                $this->db->escape($token)."\"");
        if($result->num_rows == 0){ // если токен не найден
            $this->userToken = "";
            return;
        }
        //Удаляем если время действия токена истекло
        if($result->row["data_expired"] < time()){
            $this->DeleteToken($result->row["token"]);
            setcookie('_uid', '', time() - 30);
        }
        $this->user = $result->row;
    }
    //возвращает true если пользователь залогинен
    public function IsUserLogger(){
        if($this->userToken != ""){
            return true;
        }
        return false;
    }
    
    public function GetUserName(){
        return $this->user["username"];
    }
    
    public function GetUserId(){
        return $this->user["user_id"];
    }
    
    private function DeleteToken($token){
        $this->db->query("DELETE FROM `token` WHERE token = \"".$token."\"");
    }
    
    public function Logout(){
        if($this->userToken != ""){
            $this->DeleteToken($this->userToken);
            setcookie('_uid', '', time() - 30);
        }
    }
    
    //вывод кол-во задач на текущий день (для вывода в хедер
    public function GetTaskCount(){
        if($this->IsUserLogger()){
            $date = new Datetime();
            $from = $date->format('Y-m-d').' 00:00:00';
            $date->add(new DateInterval("P1D"));
            $to = $date->format('Y-m-d').' 00:00:00';
            $sql = "SELECT COUNT(t.task_id) FROM `task` t, `task_to_user` ttu "
                . "WHERE ttu.user_id = '".$this->user["user_id"]."' AND t.task_id = ttu.task_id AND "
                    . " t.start <= '".$to."' AND t.end > '".$from."'"
                    . "ORDER BY t.start;";
            $result = $this->db->query($sql);
            return $result->row["COUNT(t.task_id)"];
        }
    }
}