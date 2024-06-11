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
$users = json_decode(file_get_contents($usersFile), true);
$warehouse = new Warehouse($itemsFile, $logsFile);

echo "Enter your name: ";
$name = (readline());
echo "Enter your password: ";
$password = (readline());
if (!login($name, $password)) {
    echo "Invalid login. Exiting...\n";
    exit;
}
while (true) {
    showMenu($warehouse);
    $option = readline();
    input($option, $warehouse);
}
function login($name, $password): bool
{
    global $users;
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
    $table->setHeaders(['Nr','Name', 'Quantity', 'Price','UUID']);
    foreach ($items as $key => $item)
    {
        $table->addRow([
              $key + 1,
              $item->name,
              $item->quantity,
              $item->price . "$",
              $item->uuid
        ]);
    }
    $table->render();
    echo "Choose an option: ";
}

function input($option, $warehouse) {
    switch ($option) {
        case 1:
            echo "Enter item number: ";
            $itemNumber = (readline());
            $item = $warehouse->getItems()[$itemNumber - 1] ?? null;
            if ($item) {
                echo "Enter item quantity: ";
                $quantity = intval(readline());
                $item->quantity += $quantity;
                $warehouse->updateItem($item->uuid, $item->name, $item->quantity, $item->price, $_SESSION['user']);
                echo "Item quantity updated.\n";
            } else {
                echo "Invalid item number.\n";
            }
            break;

        case 2:
            echo "Enter item number: ";
            $itemNumber = (readline());
            $item = $warehouse->getItems()[$itemNumber - 1] ?? null;
            if ($item) {
                echo "Enter new item name: ";
                $name = (readline());
                echo "Enter new item quantity: ";
                $quantity = (readline());
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




