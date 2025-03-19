<?php

namespace models;

class Car {
    private $id;
    private $modelId;
    private $userId;
    private $year;
    private $price;
    private $priority;
    private $status;
    private $isActive;
    private $isNew;
    private $description;
    private $addedAt;

    public function __construct($id, $modelId, $userId, $year, $price, $priority = 0, $status = 'available', $isActive = true, $isNew = false, $description = '', $addedAt = null) {
        $this->id = $id;
        $this->modelId = $modelId;
        $this->userId = $userId;
        $this->year = $year;
        $this->price = $price;
        $this->priority = $priority;
        $this->status = $status;
        $this->isActive = $isActive;
        $this->isNew = $isNew;
        $this->description = $description;
        $this->addedAt = $addedAt ? $addedAt : date('Y-m-d H:i:s');
    }

    public function getId() {
        return $this->id;
    }

    public function getModelId() {
        return $this->modelId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getYear() {
        return $this->year;
    }

    public function getPrice() {
        return $this->price;
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

    public function getIsNew() {
        return $this->isNew;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getAddedAt() {
        return $this->addedAt;
    }
}