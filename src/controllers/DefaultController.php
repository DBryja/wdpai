<?php
namespace controllers;
class DefaultController extends AppController {

   public function index(){
      $this->render("login", ["message" => "Hello World"]);
   }

   public function dashboard(){
      $this->render("dashboard");
   }

   
}