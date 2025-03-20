<?php

namespace repository;

use Database;

class Repository
{
    private static $instance = null;
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }
}