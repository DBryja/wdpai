<?php

namespace models;

class Brand {
    private $id;
    private $name;
    private $isActive;

    public function __construct($id, $name, $isActive = true) {
        $this->id = $id;
        $this->name = $name;
        $this->isActive = $isActive;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive) {
        $this->isActive = $isActive;
        return $this;
    }
}