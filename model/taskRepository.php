<?php

class TaskRepository extends Model{
    private $format = 'd.m.Y H:i';
    private $dbformat = 'Y-m-d H:i:s';
    public function AddTask($user_id, $data){
        $date1 = DateTime::createFromFormat($this->format, $this->request->post["start"]);
        $date2 = DateTime::createFromFormat($this->format, $this->request->post["end"]);
        
        $sql = "INSERT INTO `task`(name,description,start,end) "
                . "VALUES('".$this->db->escape($data["name"])."',"
                . '"'.$this->db->escape($data["description"]).'",'
                . '"'.$date1->format($this->dbformat).'",'
                . '"'.$date2->format($this->dbformat).'");';
        $this->db->query($sql);
        $task_id = $this->db->getLastId();
        $sql = "INSERT INTO `task_to_user`(user_id,task_id,email_notify,phone_notify)"
                . "VALUES(".$user_id.",".$task_id.","
                . "'".isset($this->request->post["email_notify"])."',"
                . "'".isset($this->request->post["phone_notify"])."');";
        $this->db->query($sql);
    }
    
    public function UpdateTask($data){
        $date1 = DateTime::createFromFormat($this->format, $this->request->post["start"]);
        $date2 = DateTime::createFromFormat($this->format, $this->request->post["end"]);
        
        $sql = "UPDATE `task_to_user` SET email_notify='".$this->db->escape($data["email_notify"])
                ."', phone_notify='".$this->db->escape($data["phone_notify"])."'"
                . "WHERE task_id='".$this->db->escape($data["task_id"])."';";
        $this->db->query($sql);
        $sql = "UPDATE `task` SET name='".$this->db->escape($data["name"])."', "
                . "description='".$this->db->escape($data["description"])."',"
                . "start='".$date1->format($this->dbformat)."', "
                . "end='".$date2->format($this->dbformat)."'"
                . "WHERE task_id='".$this->db->escape($data["task_id"])."';";
        $this->db->query($sql);
    }

    public function GetTaskById($id){
        $sql = "SELECT t.*, ttu.* FROM `task` t, `task_to_user` ttu "
                . "WHERE t.task_id = ttu.task_id AND t.task_id =".$id.";";
        $result = $this->db->query($sql)->row;
        if($result == null){
            return null;
        }
        //преобразуем в нужный формат даты
        $date1 = DateTime::createFromFormat($this->dbformat, $result["start"]);
        $date2 = DateTime::createFromFormat($this->dbformat, $result["end"]);
        $result["start"] = $date1->format($this->format);
        $result["end"] = $date2->format($this->format);
        return $result;
    }
    
    public function GetTasksByDate($user_id,$date){
        //формируем дату от и до
        $from = $date->format($this->dbformat);
        $date->add(new DateInterval("P1D"));
        $to = $date->format($this->dbformat);
        $sql = "SELECT t.*, ttu.* FROM `task` t, `task_to_user` ttu "
            . "WHERE ttu.user_id = '".$user_id."' AND t.task_id = ttu.task_id AND "
                . " t.start <= '".$to."' AND t.end > '".$from."'"
                . "ORDER BY t.start;";
        $result = $this->db->query($sql);
        //преобразуем даты в наш формат
        for($i=0; $i < count($result->rows); $i++){
            $date1 = DateTime::createFromFormat($this->dbformat, $result->rows[$i]["start"]);
            $date2 = DateTime::createFromFormat($this->dbformat, $result->rows[$i]["end"]);
            $result->rows[$i]["start"] = $date1->format($this->format);
            $result->rows[$i]["end"] = $date2->format($this->format);
        }
        return $result->rows;
    }
    
    public function DeleteTask($task_id){
        $sql = "DELETE FROM `task_to_user` WHERE task_id=".$task_id;
        $this->db->query($sql);
        $sql = "DELETE FROM `task` WHERE task_id=".$task_id;
        $this->db->query($sql);
    }
}