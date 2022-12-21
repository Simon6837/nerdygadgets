<?php
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
mysqli_query($databaseConnection, "START TRANSACTION");
// insert values into the orders table
$insertOrder = mysqli_prepare($databaseConnection, "INSERT INTO Orders (CustomerID, SalespersonPersonID
, PickedByPersonID, ContactPersonID, BackorderOrderID, CustomerPurchaseOrderNumber
, IsUndersupplyBackordered, LastEditedBy, OrderDate, ExpectedDeliveryDate, PickingCompletedWhen
, LastEditedWhen, comments) VALUES (1, 1, 1, 1, 1, 1, 1, 1, ?, ?, ?, ?, 'order made on nerdygadgets.nl')");

// bind values to the prepared statement and execute the query
mysqli_stmt_bind_param($insertOrder, 'ssss', $date, $dateW, $dateT, $dateT);
mysqli_stmt_execute($insertOrder);

// get the last auto-increment value from the previous query
$lastOrder = mysqli_insert_id($databaseConnection);
$cart = getCart();
// loop through each item in the cart
foreach ($cart as $ID => $quantity) {
    // get the stock item
    $stockItem = getStockItem($ID, $databaseConnection);

    // get the price of the item
    $price = $stockItem['SellPrice'];

    // insert item information into the orderlines table
    $InsertLine = mysqli_query($databaseConnection, "INSERT INTO orderlines (OrderID, StockItemID, Description,
    PackageTypeID, Quantity, UnitPrice, TaxRate, PickedQuantity, PickingCompletedWhen, LastEditedBy, LastEditedWhen) 
    VALUES ($lastOrder, $ID, 'order made on nerdygadgets.nl', 1, $quantity, $price, 15.000, $quantity, '$dateT', 1, '$dateT')");

    // update the stock item holdings by decreasing the quantity on hand
    $updateStockItemHoldings = mysqli_query($databaseConnection, "UPDATE stockitemholdings SET quantityonhand = (quantityonhand - $quantity) WHERE stockitemid = $ID");
}
// if all queries were successful, commit the transaction
if ($insertOrder && $InsertLine && $updateStockItemHoldings) {
    mysqli_query($databaseConnection, "COMMIT");
} else {
    // if any of the queries failed, roll back the transaction
    mysqli_query($databaseConnection, "ROLLBACK");
    // close the database connection
    mysqli_close($databaseConnection);
    $character = str_contains($_SERVER['HTTP_REFERER'], '?') ? '&' : '?';
    header("Location: " . $_SERVER['HTTP_REFERER'] . $character . "showErrorMessage=true");}

// close the database connection
mysqli_close($databaseConnection);

// clear the cart
clearCart();

// redirect to the Ideal demo page
header('location: https://www.ideal.nl/demo/en/?screens=dskweb&bank=rabo&type=dsk;');
