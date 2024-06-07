<?php
require 'vendor/autoload.php';
require_once 'Item.php';
use Carbon\Carbon;
class Warehouse
{
    private $items = array();
    private $filename;
    private $logsFilename;


    public function __construct($filename, $logsFilename)
    {
        $this->filename = $filename;
        $this->logsFilename = $logsFilename;
        $this->loadItems();
    }


    public function addItem(Item $item, $username)
    {
        $this->items[] = $item;
        $this->logAction('create', $item, $username);
        $this->saveItems();
    }


    public function getItems()
    {
        return $this->items;
    }

    public function findItemByUuid($uuid)
    {
        foreach ($this->items as $item) {
            if ($item->uuid == $uuid) {
                return $item;
            }
        }
        return null;
    }


    public function updateItem($uuid, $name, $quantity, $price, $username)
    {
        $item = $this->findItemByUuid($uuid);
        if ($item) {
            $item->update($name, $quantity, $price);
            $this->logAction('update', $item, $username);
            $this->saveItems();
        }
    }

    public function deleteItem($uuid, $username)
    {
        foreach ($this->items as $key => $item) {
            if ($item->uuid == $uuid) {
                unset($this->items[$key]);
                $this->logAction('delete', $item, $username);
                $this->saveItems();
                return true;
            }
        }
        return false;
    }
    private function saveItems()
    {
        $itemsArray = array();
        foreach ($this->items as $item) {
            $itemsArray[] = array(
                  'uuid' => $item->uuid,
                  'name' => $item->name,
                  'quantity' => $item->quantity,
                  'price' => $item->price,
                  'created_at' => $item->created_at,
                  'updated_at' => $item->updated_at
            );
        }
        file_put_contents($this->filename, json_encode($itemsArray, JSON_PRETTY_PRINT));
    }
    private function loadItems()
    {
        if (file_exists($this->filename)) {
            $itemsArray = json_decode(file_get_contents($this->filename), true);
            if ($itemsArray) {
                foreach ($itemsArray as $itemData) {
                    $uuid = isset($itemData['uuid']) ? $itemData['uuid'] : null;
                    $created_at = isset($itemData['created_at']) ? $itemData['created_at'] : null;
                    $updated_at = isset($itemData['updated_at']) ? $itemData['updated_at'] : null;
                    $this->items[] = new Item(
                          $itemData['name'],
                          $itemData['quantity'],
                          $itemData['price'],
                          $uuid,
                          $created_at,
                          $updated_at
                    );
                }
            }
        }
    }
    private function logAction($action, $item, $username)
    {
        $logEntry = [
              'action' => $action,
              'item' => [
                    'uuid' => $item->uuid,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price
              ],
              'username' => $username,
              'timestamp' => Carbon::now()->toDateTimeString()
        ];
        $logs = $this->loadLogs();
        $logs[] = $logEntry;
        file_put_contents($this->logsFilename, json_encode($logs, JSON_PRETTY_PRINT));
    }

    private function loadLogs()
    {
        if (file_exists($this->logsFilename)) {
            return json_decode(file_get_contents($this->logsFilename), true);
        }
        return [];
    }
}




