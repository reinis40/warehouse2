<?php
require 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class Item {
    public $uuid;
    public $name;
    public $quantity;
    public $price;
    public $created_at;
    public $updated_at;

    // Constructor for the Item class
    public function __construct($name, $quantity, $price, $uuid = null, $created_at = null, $updated_at = null) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->created_at = $created_at ?? Carbon::now()->toDateTimeString();
        $this->updated_at = $updated_at ?? Carbon::now()->toDateTimeString();
    }

    // Method to update the item
    public function update($name, $quantity, $price) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->updated_at = Carbon::now()->toDateTimeString();
    }
}



