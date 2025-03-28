<?php

namespace models;

class Model {
    private $id;
    private $brandId;
    private $name;
    private $isActive;

    public function __construct($id, $brandId, $name, $isActive = true) {
        $this->id = $id;
        $this->brandId = $brandId;
        $this->name = $name;
        $this->isActive = $isActive;
    }

    public function getId() {
        return $this->id;
    }

    public function getBrandId() {
        return $this->brandId;
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