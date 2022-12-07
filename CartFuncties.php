<?php
if (!isset($_SESSION)) {
    session_start();
}

/**
 * get the cart from the session
 * if it doesn't exist, create it
 * @return array
 */
function getCart()
{
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
}
/**
 * Remove all items from the cart
 * @return void
 */
function clearCart()
{
    $_SESSION['cart'] = array();
}

/**
 * store the cart in the session
 * @param array $cart the cart to store
 * @return void
 */
function saveCart($cart)
{
    $_SESSION["cart"] = $cart;
}

/**
 * add a product to the cart
 * if the product already exists in the cart, increase the quantity by one
 * if the product does not exist in the cart, add it
 * @param int $stockItemID the id of the product to add to the cart
 * @return void
 */
function addProductToCart($stockItemID)
{
    $cart = getCart();
    $cart[$stockItemID] = array_key_exists($stockItemID, $cart) ? $cart[$stockItemID] + 1 : 1;
    saveCart($cart);
}

/**
 * remove a product from the cart
 * if the products quantity is higher than one, decrease the quantity by one
 * if the product quantity is one, remove the product from the cart
 * @param int $stockItemID the id of the product to remove
 * @return void
 */
function removeProductFromCart($stockItemID)
{
    $cart = getCart();
    if ($cart[$stockItemID] > 1) {
        $cart[$stockItemID]--;
    } else {
        unset($cart[$stockItemID]);
    }
    saveCart($cart);
}

/**
 * this function fully removes a product from the cart regardless of the quantity
 * @param int $stockItemID the id of the product to delete
 * @return void
 */
function deleteProductFromCart($stockItemID)
{
    $cart = getCart();
    unset($cart[$stockItemID]);
    saveCart($cart);
}

/**
 * this function checks if the cart should be modified
 * if it does it wil figure out what needs to change
 * it then calls the required function
 * after it is done it will return the user to the page they were on
 * @return void
 */
function checkForModification()
{
    //if the current url contains params, set the param identifier to & to add a new param
    $character = str_contains($_SERVER['HTTP_REFERER'], '?') ? '&' : '?';
    //add an item when the user clicked the + icon
    if (isset($_GET['addId'])) {
        addProductToCart($_GET['addId']);
        // send the user back to view.php with the stockItemID as id
        header("Location: " . $_SERVER['HTTP_REFERER'] . $character . "showAddedMessage=true");
    }
    //remove an item when the user clicked the - icon
    if (isset($_GET['removeId'])) {
        removeProductFromCart($_GET['removeId']);
    }
    //delete a item when the user clicked the "Verwijder" link
    if (isset($_GET['deleteId'])) {
        deleteProductFromCart($_GET['deleteId']);
    }
    //if one of the above actions is done, remove the param from the url to prevent rerunning the action on a page reload
    if (isset($_GET['removeId']) || isset($_GET['deleteId'])) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . $character . "showDeletedMessage=true");
    }
}
