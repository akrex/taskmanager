<?php

//класс для переадресации запросов
final class RouteManager{
    public static $defaultcontroller = "home";
    public static $defaultaction = "index";
    private $registry;
    
    public function __get($key) {
            return $this->registry->get($key);
    }

    public function __set($key, $value) {
            $this->registry->set($key, $value);
    }
    
    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function RedirectToController(){
        //получаем имя контроллера и его екшн
        $controller = RouteManager::$defaultcontroller;
        if(isset($this->request->get["controller"])){
            if($this->request->get["controller"]!=""){
                $controller = $this->request->get["controller"];
            }
        }
        $action = RouteManager::$defaultaction;
        if(isset($this->request->get["action"])){
            if($this->request->get["action"]!=""){
                $action = $this->request->get["action"];
            }
        }
        //пытаемься "подключиться" по заданным параметрам
        $path = "controller/".$controller."Controller.php";
        if (file_exists($path)){
            require_once("controller/".$controller."Controller.php");
            if(!class_exists($controller."Controller")){
                $template = "view/notfound.tpl";
                include($template);
            }
            $classname = $controller."Controller";
            $controllerObject = new $classname($this->registry);
            $controllerObject->$action();
        }else{
            $template = "view/notfound.tpl";
            include($template);
        }
    }
    
    public function RedirectToAction($action, $controller, $parameters = null){
        $url = "./index.php?";
        if($controller != RouteManager::$defaultcontroller){
            $url .= "controller=".$controller;
        }
        if($action != RouteManager::$defaultaction){
            if($controller != RouteManager::$defaultcontroller){
                $url .= "&";
            }
            $url .= "action=".$action;
        }
        if($parameters != null){
            foreach($parameters as $key => $value){
                $url .= "&".$key."=";
                $url .= $value;
            }
        }
        header( 'Location: '.$url, true, 303 );
    }
}
