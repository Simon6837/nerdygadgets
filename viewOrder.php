<?php
include __DIR__ . "/header.php";
include_once "cartfuncties.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling</title>
</head>

<body>
    <?php
    $cart = getCart();
    ?>
    <h1>Bestelling</h1>
    <div class="order-main">
        <!-- cart -->
        <div class="order-cart-container">
            <?php
            foreach ($cart as $key => $item) {
                $StockItem = getStockItem($key, $databaseConnection);
                $exPrice = round($StockItem['SellPrice'] / 121 * 100, 2);
                $imagepath = ($stockItemImage = getStockItemImage($key, $databaseConnection)) ? "Public/StockItemIMG/" . $stockItemImage[0]['ImagePath'] : "Public/StockGroupIMG/" . $StockItem['BackupImagePath'];
            ?>
                <div class="order-cart-item">
                    <img src="<?php echo $imagepath; ?>" alt="image of <?php echo $StockItem['StockItemName']; ?>" class="order-cart-image">
                    <div class="cart-item-info">
                        <div class="cart-item-name"><?php echo $StockItem['StockItemName']; ?></div>
                        <div class="cart-item-price">€<?php echo $exPrice; ?></div>
                        <div class="cart-item-amount">Aantal: <?php echo $item; ?></div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <!-- customer data and order button -->
        <div class="order-details">
            <div class="order-customer">
                <h2>Klant</h2>
                <table>
                    <tr>
                        <td>Naam:</td>
                        <td>John Doe</td>
                    </tr>
                    <tr>
                        <td>Adres:</td>
                        <td>Straatnaam 12</td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td>test@example.com</td>
                    </tr>
                    <tr>
                        <td>Woonplaats:</td>
                        <td>New York</td>
                </table>
            </div>
            <div class="order-buttons">
                <?php
                if (count($cart) > 0) {
                ?>
                    <a href="Order.php">
                        <button class="btn btn-danger order-button">Order</button>
                    <?php
                } else {
                    ?>
                        <button class="btn btn-danger order-button" disabled>Order</button>
                    <?php
                }
                    ?>
                    </a>
            </div>
        </div>
        </main>
</body>

</html>