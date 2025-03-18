<?php

namespace repository;
//require_once __DIR__ . '/../../Database.php';
use Database;
class Repository
{
    protected $db;
//    TODO: Zrobić singletona
    public function __construct()
    {
        $this->db = new Database();
    }
}