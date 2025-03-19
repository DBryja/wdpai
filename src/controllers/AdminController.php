<?php
namespace controllers;

class AdminController extends AppController {

    public function admin() {
        $this->render("admin");
    }

    public function admin_users() {
        $this->render("admin-users");
    }

    public function admin_cars() {
        $this->render("admin-cars");
    }

    public function admin_editCar() {
        $this->render("admin-editCar");
    }
}