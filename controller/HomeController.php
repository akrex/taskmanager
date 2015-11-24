<?php

class HomeController extends Controller{
    
    public function index(){
        $this->data["title"] = "Главная";
        $this->data["section"] = "Home";
        $this->View();
    }
    
    public function about(){
        $this->data["title"] = "О нас";
        $this->data["section"] = "About";
        $this->View();
    }
}
