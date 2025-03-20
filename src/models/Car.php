<?php

namespace models;
class Car {
    private $id;
    private $modelId;
    private $price;
    private $year;
    private $isNew;
    private $priority;
    private $status;
    private $isActive;
    private $images;
    private $addedAt;

    public function __construct($id, $modelId, $price, $year, $isNew, $priority = 0, $status = 'available', $isActive = true, $images = null, $addedAt = null) {
        $this->id = $id;
        $this->modelId = $modelId;
        $this->price = $price;
        $this->year = $year;
        $this->isNew = $isNew;
        $this->priority = $priority;
        $this->status = $status;
        $this->isActive = $isActive;
        $this->images = $images;
        $this->addedAt = $addedAt ? $addedAt : date('Y-m-d H:i:s');
    }

    public function getId() {
        return $this->id;
    }

    public function getModelId() {
        return $this->modelId;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getYear() {
        return $this->year;
    }

    public function getIsNew() {
        return $this->isNew;
    }

    public function getPriority() {
        return $this->priority;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function getImages() {
        return $this->images;
    }

    public function getAddedAt() {
        return $this->addedAt;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    public function setIsActive($isActive) {
        $this->isActive = $isActive;
        return $this;
    }

    public function setPriority($priority) {
        $this->priority = $priority;
        return $this;
    }
}