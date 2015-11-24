<?php

final class Loader{
    protected $registry;

    public function __construct($registry) {
            $this->registry = $registry;
    }

    public function __get($key) {
            return $this->registry->get($key);
    }

    public function __set($key, $value) {
            $this->registry->set($key, $value);
    }
    
    public function model($model) {
        //составляем пусть к модели
        $file  = 'model/' . $model . '.php';
        if (file_exists($file)) { 
            //подключаем модель
            include_once($file);
            // добавляеем его в глобальные регистр
            $this->registry->set('model_' . str_replace('/', '_', $model), new $model($this->registry));
        } else {
            trigger_error('Error: Could not load model ' . $model . '!');
            exit();					
        }
    }
}
