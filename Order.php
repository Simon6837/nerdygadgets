<?php
// include __DIR__ . "/header.php";
include "database.php";
$databaseConnection = connectToDatabase();
include_once 'CartFuncties.php';

// Gets current date
$date = date('Y-m-d');

// Gets current date and then adds a specific amount of time
$dateW = date('Y-m-d', strtotime(date('Y-m-d') . '+1 week'));

// Gets current date and time
$dateT = date('Y-m-d H:i:s');

//connects to database
$databaseConnection = connectToDatabase();

// inserts values from the order to the orders table
$insertOrder = mysqli_prepare($databaseConnection, "INSERT INTO Orders (CustomerID, SalespersonPersonID
, PickedByPersonID, ContactPersonID, BackorderOrderID, CustomerPurchaseOrderNumber
, IsUndersupplyBackordered, LastEditedBy, OrderDate, ExpectedDeliveryDate, PickingCompletedWhen
, LastEditedWhen, comments) VALUES (1, 1, 1, 1, 1, 1, 1, 1, ?, ?, ?, ?, 'hallo')");

// gives the values for the ?'s in the query
mysqli_stmt_bind_param($insertOrder, 'ssss', $date, $dateW, $dateT, $dateT);

// executes the query with the given values
mysqli_stmt_execute($insertOrder);

// Gets last auto-increment from last query
$lastOrder = mysqli_insert_id($databaseConnection);

//closes connection with the database
mysqli_close($databaseConnection);

// gets item from cart
$cart = getCart();

$databaseConnection = connectToDatabase();

// Loops through every item in the cart
foreach ($cart as $ID => $quantity) {
    // gets the stockitem
    $stockItem = getStockItem($ID, $databaseConnection);

    //Gets the price of the corresponding item
    $price = $stockItem['SellPrice'];

    //LastEditedBy column is dummyvalue, we don't have personID's yet
    // inserts item information for all items in the cart to the orderlines table
    $InsertLine = mysqli_query($databaseConnection, "INSERT INTO orderlines (OrderID, StockItemID, Description,
    PackageTypeID, Quantity, UnitPrice, TaxRate, PickedQuantity, PickingCompletedWhen, LastEditedBy, LastEditedWhen) 
    VALUES ($lastOrder, $ID, 'order made on nerdygadgets.nl', 1, $quantity, $price, 15.000, $quantity, '$dateT', 1, '$dateT')");
    
    // Updates storage by detracting the quantity of the purchased item from the current storage quantity
   $updateStockItemHoldings = mysqli_query($databaseConnection, "UPDATE stockitemholdings SET quantityonhand = (quantityonhand - $quantity) WHERE stockitemid = $ID");
}

mysqli_close($databaseConnection);

//remove all items from cart
clearCart();

// Opens the Ideal demo page when executed
header('location: https://www.ideal.nl/demo/en/?screens=dskweb&bank=rabo&type=dsk;');
