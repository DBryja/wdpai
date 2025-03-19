<?php

namespace models;

class Model {
    private $id;
    private $brandId;
    private $name;
    private $bodyType;

    public function __construct($id, $brandId, $name, $bodyType) {
        $this->id = $id;
        $this->brandId = $brandId;
        $this->name = $name;
        $this->bodyType = $bodyType;
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

    public function getBodyType() {
        return $this->bodyType;
    }
}
