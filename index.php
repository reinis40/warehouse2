<?php
require 'vendor/autoload.php';
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
require_once 'Warehouse.php';
require_once 'Item.php';
require_once 'User.php';

$usersFile = 'users.json';
$itemsFile = 'items.json';
$logsFile = 'logs.json';

$warehouse = new Warehouse($itemsFile, $logsFile);

session_start();

echo "Enter your name: ";
$name = readline();
echo "Enter your password: ";
$password = readline();
if (!login($name, $password, $usersFile)) {
    echo "Invalid login. Exiting...\n";
    exit;
}

while (true) {
    showMenu($warehouse);
    $option = readline();
    handleInput($option, $warehouse, $logsFile);
}

function login($name, $password, $usersFile): bool
{
    $users = json_decode(file_get_contents($usersFile), true);
    foreach ($users as $userData) {
        if ($userData['name'] == $name && $userData['password'] == $password) {
            $_SESSION['user'] = $name;
            return true;
        }
    }
    return false;
}

function showMenu($warehouse)
{
    echo "Warehouse Management System\n";
    echo "1. Add Item\n";
    echo "2. Update Item\n";
    echo "3. Delete Item\n";
    echo "4. Show Logs\n";
    echo "5. Show Total Items and Cost\n";
    echo "6. Exit\n";
    $items = $warehouse->getItems();
    $output = new ConsoleOutput();
    $table = new Table($output);
    $table->setHeaders(['Nr', 'Name', 'Quantity', 'Price', 'UUID']);
    foreach ($items as $key => $item) {
        $table->addRow([
            $key + 1,
            $item->name,
            $item->quantity,
            $item->price . "$",
            $item->uuid,
        ]);
    }
    $table->render();
    echo "Choose an option: ";
}

function handleInput($option, $warehouse, $logsFile) {
    switch ($option) {
        case 1:
            echo "Enter item name: ";
            $name = readline();
            echo "Enter item quantity: ";
            $quantity = intval(readline());
            echo "Enter item price: ";
            $price = floatval(readline());
            $item = new Item($name, $quantity, $price);
            $warehouse->addItem($item, $_SESSION['user']);
            echo "Item added.\n";
            break;

        case 2:
            echo "Enter item number: ";
            $itemNumber = intval(readline());
            $item = $warehouse->getItems()[$itemNumber - 1] ?? null;
            if ($item) {
                echo "Enter new item name: ";
                $name = readline();
                echo "Enter new item quantity: ";
                $quantity = intval(readline());
                echo "Enter new item price: ";
                $price = floatval(readline());
                $warehouse->updateItem($item->uuid, $name, $quantity, $price, $_SESSION['user']);
                echo "Item updated.\n";
            } else {
                echo "Invalid item number.\n";
            }
            break;

        case 3:
            echo "Enter item number: ";
            $itemNumber = intval(readline());
            $item = $warehouse->getItems()[$itemNumber - 1] ?? null;
            if ($item) {
                $warehouse->deleteItem($item->uuid, $_SESSION['user']);
                echo "Item deleted.\n";
            } else {
                echo "Invalid item number.\n";
            }
            break;

        case 4:
            $logs = json_decode(file_get_contents($logsFile), true);
            foreach ($logs as $log) {
                echo "Action: {$log['action']}, Item: {$log['item']['name']}, User: {$log['username']}, Timestamp: {$log['timestamp']}\n";
            }
            break;

        case 5:
            $totals = $warehouse->getTotalItemsAndCost();
            echo "Total Items: {$totals['totalQuantity']}, Total Cost: {$totals['totalCost']}\n";
            break;

        case 6:
            echo "Exiting...\n";
            exit;

        default:
            echo "Invalid option. Please try again.\n";
            break;
    }
}
