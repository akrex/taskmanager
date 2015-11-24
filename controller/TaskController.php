<?php

class TaskController  extends Controller{
    private $format = 'd.m.Y H:i';
    
    public function index(){
        if($this->user->IsUserLogger()==false){
                $this->routemanager->RedirectToAction('index','home');
        }
        $this->data["title"] = "Задачи";
        $this->data["section"] = "Task";
        
        $this->View();
    }
    
    public function taskList(){
        if($this->user->IsUserLogger()==true){
            if(isset($this->request->get["time"])){
                $this->loader->model("taskRepository");
                $date = new DateTime();
                $date->setTimestamp(substr($this->request->get["time"],0,10));
                $this->data["task"] = $this->model_taskRepository->GetTasksByDate($this->user->GetUserId(),$date);
                $this->AjaxView();
            }
        }
    }
    
    public function update(){
        if($this->user->IsUserLogger()==false){
                $this->routemanager->RedirectToAction('index','home');
        }
        if($this->request->server['REQUEST_METHOD']=='GET' && !isset($this->request->get["task"])){
                $this->routemanager->RedirectToAction('index','task');
        }
        $this->loader->model("taskRepository");
        if($this->request->server['REQUEST_METHOD']=='GET'){
            $task_id = $this->request->get["task"];
        }else{
            $task_id = $this->request->post["task_id"];
        }
        $task = $this->model_taskRepository->GetTaskById($task_id);
        //если пользователь пытаеться отредактировать не свою таску
        if($task["user_id"]!=$this->user->GetUserId()){
            $this->routemanager->RedirectToAction('index','task');
        }
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            //если все данные введены корректно
            if($this->ValidateAddForm()){
                if(isset($this->request->post["email_notify"])){
                    $this->request->post["email_notify"] = 1;
                }
                if(isset($this->request->post["phone_notify"])){
                    $this->request->post["phone_notify"] = 1;
                }
                //подключаем модель и регестрируем пользователя\
                $this->model_taskRepository->UpdateTask($this->request->post);
                $this->routemanager->RedirectToAction('index','task', array("msg"=>"as"));
            }
            $this->data["task_id"] = $this->request->post["task_id"];
            $this->data["name"] = $this->request->post["name"];
            $this->data["description"] = $this->request->post["description"];
            $this->data["start"] = $this->request->post["start"];
            $this->data["end"] = $this->request->post["end"];
            $this->data["email_notify"] = $this->request->post["email_notify"];
            $this->data["phone_notify"] = $this->request->post["phone_notify"];
        }else{
            $this->data["task_id"] = $task["task_id"];
            $this->data["name"] = $task["name"];
            $this->data["description"] = $task["description"];
            $this->data["start"] = $task["start"];
            $this->data["end"] = $task["end"];
            $this->data["email_notify"] = $task["email_notify"]==0?null:1;
            $this->data["phone_notify"] = $task["phone_notify"]==0?null:1;
        }
        
        $this->data["title"] = "Редактировать задачу";
        $this->text["name"] = "Название задачи";
        $this->text["description"] = "Описание задачи";
        $this->text["start"] = "Начало";
        $this->text["end"] = "Конец";
        $this->text["email_notify"] = "Уведомить на email";
        $this->text["phone_notify"] = "Уведомить на телефон";
        $this->View();
    }


    public function Add(){
        if($this->user->IsUserLogger()==false){
                $this->routemanager->RedirectToAction('index','home');
        }
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            //если все данные введены корректно
            if($this->ValidateAddForm()){
                //подключаем модель и регестрируем пользователя\
                $this->loader->model("taskRepository");
                $this->model_taskRepository->AddTask($this->user->GetUserId(),$this->request->post);
                $this->routemanager->RedirectToAction('index','task', array("msg"=>"as"));
            }
            $this->data["name"] = $this->request->post["name"];
            $this->data["description"] = $this->request->post["description"];
            $this->data["start"] = $this->request->post["start"];
            $this->data["end"] = $this->request->post["end"];
            $this->data["email_notify"] = $this->request->post["email_notify"];
            $this->data["phone_notify"] = $this->request->post["phone_notify"];
        }else{
            //инициализация дат (ближайщий час)
            $format = 'd.m.Y H';
            $data = new DateTime();
            $data->add(DateInterval::createfromdatestring('+1 hour'));
            $this->data["start"] = $data->format($format).'.00';
            $data->add(DateInterval::createfromdatestring('+1 hour'));
            $this->data["end"] = $data->format($format).'.00';
        }
        
        $this->data["title"] = "Добавить задачу";
        $this->text["name"] = "Название задачи";
        $this->text["description"] = "Описание задачи";
        $this->text["start"] = "Начало";
        $this->text["end"] = "Конец";
        $this->text["email_notify"] = "Уведомить на email";
        $this->text["phone_notify"] = "Уведомить на телефон";
        $this->View();
    }
    
    
    public function delete(){
        if($this->user->IsUserLogger()==false){
                $this->routemanager->RedirectToAction('index','home');
        }
        if($this->request->server['REQUEST_METHOD']=='GET' && !isset($this->request->get["task"])){
                $this->routemanager->RedirectToAction('index','task');
        }
        $this->loader->model("taskRepository");
        if($this->request->server['REQUEST_METHOD']=='GET'){
            $task_id = $this->request->get["task"];
        }else{
            $task_id = $this->request->post["task_id"];
        }
        $task = $this->model_taskRepository->GetTaskById($task_id);
        //если пользователь пытаеться отредактировать не свою таску
        if($task["user_id"]!=$this->user->GetUserId()){
            $this->routemanager->RedirectToAction('index','task');
        }
        $this->model_taskRepository->DeleteTask($task_id);
        $this->routemanager->RedirectToAction('index','task');
    }
    
    private function ValidateAddForm(){
        if(!isset($this->request->post["name"])|| $this->request->post["name"]== ""){
            $this->error["name"] = "Название задачи обязательно!";
        }else if(!Validator::IsCorrectLenght($this->request->post["name"],3,256)){
            $this->error["name"] = "Название задачи должно быть от 3х до 256 символов";
        }
        if(isset($this->request->post["description"]) && $this->request->post["description"]!= ""){
            if(!Validator::IsCorrectLenght($this->request->post["description"],1,512)){
                $this->error["description"] = "Описание должно быть от 3х до 512 символов";
            }
        }
        
        if(!isset($this->request->post["start"]) && $this->request->post["start"]!= ""){
            $this->error["start"] = "Дата начала обязатлельна!";
        }else{
            $date1 = DateTime::createFromFormat($this->format, $this->request->post["start"]);
            if($date1==null){
                $this->error["start"] = "Некорректное значение даты!";
            }
        }
        
        if(!isset($this->request->post["end"]) || $this->request->post["end"]== ""){
            $this->error["end"] = "Дата окончания обязатлельна!";
        }else{
            $date2 = DateTime::createFromFormat($this->format, $this->request->post["end"]);
            if($date2==null){
                $this->error["end"] = "Некорректное значение даты!";
            }
        }
        
        if($date1 != null && $date2 != null){
            if($date1 > $date2){
                $this->error["start"] = "Дата начала должна быть меньше даты окончания!";
            }
        }
        
        if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
    }
}