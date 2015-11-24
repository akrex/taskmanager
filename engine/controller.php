<?php

class Controller{
    private $controllerName;
    protected $data;
    protected $layout = "view/_layers/_layout.tpl";
    protected $callerMethod;
    protected $registry;
    protected $error;
    protected $text;

    public function __construct($registry) {
        $name = get_class($this);
        $this->controllerName = str_replace("Controller", "", $name);
        $this->data = array();
        $this->error = array();
        $this->text = array();
        $this->registry = $registry;
    }

    public function __get($key) {
            return $this->registry->get($key);
    }

    public function __set($key, $value) {
            $this->registry->set($key, $value);
    }
    
    public function __call($name, $arguments) {
        $this->View();
    }
    
    protected function View(){
        //узнаём какой метод вызвал рендер и запоминаем его
        $this->callerMethod = $this->GetCallerMethodName();
        //сохроняем ошибки в спец переменной
        $this->data["error"] = $this->error;
        $this->data["text"] = $this->text;
        // распаковываем data в глобальные переменные - чтоб было удобней выводить
        extract($this->data);
        //выводим шаблон 
        include($this->layout);
    }
    
    //вывод ajax без заголовков
    protected function AjaxView(){
        //узнаём какой метод вызвал рендер и запоминаем его
        $this->callerMethod = $this->GetCallerMethodName();
        //сохроняем ошибки в спец переменной
        $this->data["error"] = $this->error;
        $this->data["text"] = $this->text;
        // распаковываем data в глобальные переменные - чтоб было удобней выводить
        $template = "view/".$this->controllerName."/ajax/".$this->callerMethod.".tpl";
        // распаковываем data в глобальные переменные - чтоб было удобней выводить
        extract($this->data);
        //выводим сам шаблон
        if (file_exists($template)){
            include($template);
        }else{
            $template = "view/notfound.tpl";
            include($template);
        }
    }
    
    protected function RenderBody(){
        //формируем путь шаблона
        $template = "view/".$this->controllerName."/".$this->callerMethod.".tpl";
        // распаковываем data в глобальные переменные - чтоб было удобней выводить
        extract($this->data);
        //выводим сам шаблон
        if (file_exists($template)){
            include($template);
        }else{
            $template = "view/notfound.tpl";
            include($template);
        }
    }
    
    //получаем имя метода вызывающего класса используя стек вызовов
    private function GetCallerMethodName(){
        $trace = debug_backtrace();
        return $trace[2]["function"];
    }
    
    
    private function RenderPartical($path){
        $template = "view/".$path;
        include($template);
    }
}