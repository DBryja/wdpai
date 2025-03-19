<?php
namespace controllers;
class DefaultController extends AppController {

   public function index(){
      $this->render("homepage");
   }

   public function admin(){
      $this->render("admin");
   }

   public function dashboard(){
      $this->render("dashboard");
   }

    public function adminLogin(){
        $this->render("admin-login");
    }
}