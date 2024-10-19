<?php
// server/room.php

class RoomServer {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function run() {
        // Your server logic here
    }
}