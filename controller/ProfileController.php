<?php

class ProfileController extends Controller{
    //словарь сообщений
    protected $msg = array(
        "rs"=>"Вы успешно зарегестрировались! Теперь вы можете войти",
        "ds"=>"Даные успешно сохранены",
        "ac"=>"Ваш email успешно подтверждён!",
        "rm"=>"Инструкция по востановлению пароля отправлена на Email.",
        "rc"=>"Пароль успешно изменён! Теперь вы можете войти");
    
    public function login(){
        $this->data["title"] = "Вход";
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            if($this->validateLoginForm()){
                if($this->user->login($this->request->post["username"],
                    $this->request->post["password"])){
                    $this->routemanager->RedirectToAction('index','home', array("msg"=>"rs"));
                }else{
                    $this->error["username"] = "Неверное имя пользователя или пароль";
                }
            }
            $this->data["username"] = $this->request->post["username"];
        }
        
        $this->data["section"] = "Login";
        $this->text["username"] = "Имя пользователя";
        $this->text["password"] = "Пароль";
        $this->View();
    }
    
    public function register(){
        $this->loader->model("userRepository");
        //если это POST запрос
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            //если все данные введены корректно
            if($this->validateRegistratiomForm()){
                //подключаем модель и регестрируем пользователя
                $this->model_userRepository->AddUser($this->request->post);
                $this->routemanager->RedirectToAction('login','profile', array("msg"=>"rs"));
            }
            $this->data["username"] = $this->request->post["username"];
            $this->data["FIO"] = $this->request->post["FIO"];
            $this->data["email"] = $this->request->post["email"];
            $this->data["phone"] = $this->request->post["phone"];
        }
        $this->data["title"] = "Регистрация";
        $this->data["section"] = "Registration";
        $this->text["username"] = "Имя пользователя";
        $this->text["password"] = "Пароль";
        $this->text["password_repeat"] = "Повторите пароль";
        $this->text["FIO"] = "Имя и фамилия";
        $this->text["email"] = "Email";
        $this->text["phone"] = "Телефон";
        $this->View();
    }
    
    public function logout(){
        if($this->user->IsUserLogger()){
            $this->user->Logout();
        }
        $this->routemanager->RedirectToAction('index','home');
    }
    
    public function profile(){
        if($this->user->IsUserLogger()==false){
                $this->routemanager->RedirectToAction('login','profile');
        }
        $this->loader->model("userRepository");
        $dbuser = $this->model_userRepository->GetUserById($this->user->GetUserId());
        //если это POST запрос
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            //если все данные введены корректно
            if($this->validateProfileForm($dbuser)){
                $this->model_userRepository->UpdateUser($this->user->GetUserId(),$this->request->post);
                $this->routemanager->RedirectToAction('profile','profile', array("msg"=>"ds"));
            }
            $this->data["username"] = $this->request->post["username"];
            $this->data["FIO"] = $this->request->post["FIO"];
            $this->data["email"] = $this->request->post["email"];
            $this->data["phone"] = $this->request->post["phone"];
        }
        $this->data["title"] = "Настройка пользователя";
        $this->data["section"] = "Profile";
        $this->text["username"] = "Имя пользователя";
        $this->text["password"] = "Пароль";
        $this->text["password_repeat"] = "Повторите пароль";
        $this->text["FIO"] = "Имя и фамилия";
        $this->text["email"] = "Email";
        $this->text["phone"] = "Телефон";
        $this->data["email_checked"] = $dbuser["email_checked"];
        if($this->request->server['REQUEST_METHOD'] != 'POST'){
            $this->data["username"] = $dbuser["username"];
            $this->data["FIO"] = $dbuser["FIO"];
            $this->data["email"] = $dbuser["email"];
            $this->data["phone"] = $dbuser["phone"];
        }
        $this->View();
    }
    
    private function validateLoginForm(){
        if($this->request->post["username"] == ""){
            $this->error["username"] = "Введите имя пользователя";
        }
        if($this->request->post["password"] == ""){
            $this->error["password"] = "Введите пароль";
        }
        
        if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
    }
    
    public function vereficationEmail(){
        if(isset($this->request->get["key"])){
            $this->loader->model("userRepository");
            if($this->model_userRepository->VereficateUser($this->request->get["key"])){
                $this->routemanager->RedirectToAction('profile','profile', array("msg"=>"ac"));
            }
        }
        $this->routemanager->RedirectToAction('profile','profile');
    }
    
    public function restore(){
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            $this->loader->model("userRepository");
            if($this->validateRestoreForm()){
                $this->model_userRepository->restoreByEmail($this->request->post["email"]);
                $this->routemanager->RedirectToAction('login','profile', array("msg"=>"rm"));
            }
            $this->data["email"] = $this->request->post["email"];
        }
        $this->data["title"] = "Востановление пароля";
        $this->text["email"] = "Email";
        $this->View();
    }
    
    public function restorestep2(){
        //для востановления обязателен ключ
        if(!isset($this->request->get["key"])){
            $this->routemanager->RedirectToAction('login','profile');
        }
        $this->loader->model("userRepository");
        //обязательно чтоб токен существовал в системе
        if(!$this->model_userRepository->isRestoreTokenExist($this->request->get["key"])){
            $this->routemanager->RedirectToAction('login','profile');
        }
        if($this->request->server['REQUEST_METHOD'] == 'POST'){
            if($this->validateRestore2Form()){
                $this->model_userRepository->updatePasswordByToken($this->request->get["key"],$this->request->post["password"]);
                $this->routemanager->RedirectToAction('login','profile', array("msg"=>"rc"));
            }
        }
        $this->data["key"] = $this->request->get["key"];
        $this->data["title"] = "Востановление пароля";
        $this->text["password"] = "Пароль";
        $this->text["password_repeat"] = "Повтор пароля";
        $this->view();
    }
    
    
    private function validateRestore2Form(){
        if(!isset($this->request->post["password"]) || $this->request->post["password"] == ""){
            $this->error["password"] = "Введите пароль";
        }else if(!Validator::IsCorrectLenght($this->request->post["password"], 6, 32)){
            $this->error["password"] = "Пароль должен содержать не менее 6 символов";
        }
        if(!isset($this->request->post["password_repeat"]) || $this->request->post["password_repeat"] == ""){
            $this->error["password_repeat"] = "Введите пароль повторно";
        }else if($this->request->post["password"]!=$this->request->post["password_repeat"]){
            $this->error["password"] = "Пароли не совпадают";
            $this->error["password_repeat"] = "Пароли не совпадают";
        }
        if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
    }
    
    private function validateRestoreForm(){
        if(!isset($this->request->post["email"]) || $this->request->post["email"] == ""){
            $this->error["email"] = "Email обязателен";
        }else if(!Validator::IsCorrectEmail($this->request->post["email"])){
            $this->error["email"] = "Неверный формат email";
        }else if(!$this->model_userRepository->IsUniqueEmail($this->request->post["email"])){
            $this->error["email"] = "Email не найден в системе!";
        }
    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
    }
    
    private function validateRegistratiomForm(){
        if(!isset($this->request->post["username"])){
            $this->error["username"] = "Имя пользователя обязательно!";
        }else if(!Validator::IsCorrectLenght($this->request->post["username"],3,64)){
            $this->error["username"] = "Имя пользователя должно быть от 3х до 64 символов";
        }else if(!Validator::IsCorrectUserName($this->request->post["username"])){
            $this->error["username"] = "Имя пользователя содержит недопустимые символы";
        }else if($this->model_userRepository->IsUniqueUsername($this->request->post["username"])){
            $this->error["username"] = "Данное имя пользователя уже занято";
        }
        
        if(!isset($this->request->post["password"]) || $this->request->post["password"] == ""){
            $this->error["password"] = "Введите пароль";
        }else if(!Validator::IsCorrectLenght($this->request->post["password"], 6, 32)){
            $this->error["password"] = "Пароль должен содержать не менее 6 символов";
        }
        if(!isset($this->request->post["password_repeat"]) || $this->request->post["password_repeat"] == ""){
            $this->error["password_repeat"] = "Введите пароль повторно";
        }else if($this->request->post["password"]!=$this->request->post["password_repeat"]){
            $this->error["password"] = "Пароли не совпадают";
            $this->error["password_repeat"] = "Пароли не совпадают";
        }
        if(!isset($this->request->post["FIO"]) || $this->request->post["FIO"] == ""){
            $this->error["FIO"] = "Имя и фамилия обязательны к вводу";
        }else if(!Validator::IsCorrectLenght($this->request->post["FIO"],3,128)){
            $this->error["FIO"] = "Имя и фамилия должны быть от 3х до 128 символов!";
        }
        
        if(!isset($this->request->post["email"]) || $this->request->post["email"] == ""){
            $this->error["email"] = "Email обязателен";
        }else if(!Validator::IsCorrectEmail($this->request->post["email"])){
            $this->error["email"] = "Неверный формат email";
        }else if($this->model_userRepository->IsUniqueEmail($this->request->post["email"])){
            $this->error["email"] = "Данный email уже используеться";
        }
        
        if(isset($this->request->post["phone"]) && $this->request->post["phone"] != ""){
            if(!Validator::IsCorrectLenght($this->request->post["phone"],8,128)){
                $this->error["phone"] = "Телефон должен быть от 8 до 128 символов";
            }else if(Validator::IsCorrectPhone($this->request->post["phone"])){
                $this->error["phone"] = "Неверный формат телефона!";
            }
        }
        
    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
    }
    
    private function validateProfileForm($dbuser){
        if($this->request->post["password"] != ""){
            if(!Validator::IsCorrectLenght($this->request->post["password"], 6, 32)){
                $this->error["password"] = "Пароль должен содержать не менее 6 символов";
            }
            else if($this->request->post["password_repeat"] == ""){
                  $this->error["password_repeat"] = "Введите пароль повторно";
            }else if($this->request->post["password"]!=$this->request->post["password_repeat"]){
                $this->error["password"] = "Пароли не совпадают";
                $this->error["password_repeat"] = "Пароли не совпадают";
            }
        }

        if(!isset($this->request->post["FIO"]) || $this->request->post["FIO"] == ""){
            $this->error["FIO"] = "Имя и фамилия обязательны к вводу";
        }else if(!Validator::IsCorrectLenght($this->request->post["FIO"],3,128)){
            $this->error["FIO"] = "Имя и фамилия должны быть от 3х до 128 символов!";
        }
        
        if(!isset($this->request->post["email"]) || $this->request->post["email"] == ""){
            $this->error["email"] = "Email обязателен";
        }else if(!Validator::IsCorrectEmail($this->request->post["email"])){
            $this->error["email"] = "Неверный формат email";
        }else if($dbuser["email"]!=$this->request->post["email"]){
            if($this->model_userRepository->IsUniqueEmail($this->request->post["email"])){
                $this->error["email"] = "Данный email уже используеться";
            }
        }
        
        if(isset($this->request->post["phone"]) && $this->request->post["phone"] != ""){
            if(!Validator::IsCorrectLenght($this->request->post["phone"],8,128)){
                $this->error["phone"] = "Телефон должен быть от 8 до 128 символов";
            }else if(Validator::IsCorrectPhone($this->request->post["phone"])){
                $this->error["phone"] = "Неверный формат телефона!";
            }
        }
        
    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
    }
}