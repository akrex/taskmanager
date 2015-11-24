<?php

final class DataBase{
    private $link;
    
    public function __construct($hostname, $username, $password, $database) {
        if (!$this->link = mysql_connect($hostname, $username, $password)) {
      		trigger_error('Error: Could not make a database link using ' . $username . '@' . $hostname);
		}

    	if (!mysql_select_db($database, $this->link)) {
      		trigger_error('Error: Could not connect to database ' . $database);
    	}
		
		mysql_query("SET NAMES 'utf8'", $this->link);
		mysql_query("SET CHARACTER SET utf8", $this->link);
		mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->link);
		mysql_query("SET SQL_MODE = ''", $this->link);
    }
    
    public function query($sql) {
        //если есть соиденение
        if ($this->link) {
            // делаем запрос
            $resource = mysql_query($sql, $this->link);
            // если запрос успешен
            if ($resource) {
                if (is_resource($resource)) {
                    $i = 0;

                    $data = array();
                    //заполняем массив данными
                    while ($result = mysql_fetch_assoc($resource)) {
                            $data[$i] = $result;
                            $i++;
                    }
                    // чиститм память
                    mysql_free_result($resource);

                    $query = new stdClass();
                    $query->row = isset($data[0]) ? $data[0] : array();
                    $query->rows = $data;
                    $query->num_rows = $i;
                    //чистим память
                    unset($data);

                    return $query;	
                } else {
                    return true;
                }
            } else {
                    print_r('Error: ' . mysql_error($this->link) . '<br />Error No: ' . mysql_errno($this->link) . '<br />' . $sql);
                    exit();
            }
        }
    }
    
    //функция для экранирования спец символов, если они были
    public function escape($value) {
        if ($this->link) {
            return mysql_real_escape_string($value, $this->link);
        }
    }
    
    //для получения последнего ID при инсёрте
    public function getLastId() {
        if ($this->link) {
            return mysql_insert_id($this->link);
        }
    }	
    
    public function __destruct() {
            if ($this->link) {
                    mysql_close($this->link);
            }
    }
}

