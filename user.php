<?php
require 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;
class User {
    public string $uuid;
    public string $name;
    public string $password;


    public function __construct($name, $password, $uuid = null) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->password = $password;
    }

}


