<?php
require_once __DIR__ . '/connect.php';

class Model {
    protected $db;

    public function __construct() {
        $this->db = Connect::getInstance()->getConnection();
    }
}
