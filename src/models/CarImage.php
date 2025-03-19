<?php

namespace models;

class CarImage {
    private $id;
    private $carId;
    private $imageUrl;
    private $altText;

    public function __construct($id, $carId, $imageUrl, $altText) {
        $this->id = $id;
        $this->carId = $carId;
        $this->imageUrl = $imageUrl;
        $this->altText = $altText;
    }

    public function getId() {
        return $this->id;
    }

    public function getCarId() {
        return $this->carId;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function getAltText() {
        return $this->altText;
    }
}
