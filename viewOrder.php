<?php
include_once __DIR__ . "/header.php";
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
    if (!empty($_SESSION['postInfo'])) {
        $_SESSION["emailaddress"] = $_SESSION['postInfo']["E-mail"];
        $_SESSION["fullname"] = $_SESSION['postInfo']["naam"];
        $_SESSION["address"] = $_SESSION['postInfo']["adres"];
        $_SESSION["residence"] = $_SESSION['postInfo']["woonplaats"];
        $_SESSION["housenumber"] = $_SESSION['postInfo']["huisnummer"];
        $_SESSION["addition"] = $_SESSION['postInfo']["huisnummerT"];
        $_SESSION["ZIP_code"] = $_SESSION['postInfo']["postcode"];
    }

    if (!empty($_SESSION['userdata'])) {
        $_SESSION["emailaddress"] = $_SESSION['userdata']["emailaddress"];
        $_SESSION["fullname"] = $_SESSION['userdata']["fullname"];
        $_SESSION["address"] = $_SESSION['userdata']["address"];
        $_SESSION["residence"] = $_SESSION['userdata']["residence"];
        $_SESSION["housenumber"] = $_SESSION['userdata']["housenumber"];
        $_SESSION["addition"] = $_SESSION['userdata']["addition"];
        $_SESSION["ZIP_code"] = $_SESSION['userdata']["ZIP_code"];
    }
    $cart = getCart();
    ?>

    <div class="order-main" />
    <!-- cart -->
    <div class="order-cart-container">
        <h2>Bestelling</h2>
        <?php
        $total = 0;
        foreach ($cart as $key => $item) {
            $StockItem = getStockItem($key, $databaseConnection);
            $exPrice = round($StockItem['SellPrice'] / 121 * 100, 2);
            $total += $exPrice * $item;
            $imagepath = ($stockItemImage = getStockItemImage($key, $databaseConnection)) ? "Public/StockItemIMG/" . $stockItemImage[0]['ImagePath'] : "Public/StockGroupIMG/" . $StockItem['BackupImagePath'];
        ?>
            <div class="order-cart-item">
                <img src="<?php echo $imagepath; ?>" alt="image of <?php echo $StockItem['StockItemName']; ?>" class="order-cart-image">
                <div class="cart-item-info">
                    <div class="cart-item-name"><?php echo $StockItem['StockItemName']; ?></div>
                    <div class="cart-item-price">€<?php echo $exPrice; ?> per stuk</div>
                    <div class="cart-item-amount">Aantal: <?php echo $item; ?></div>
                </div>
            </div>
        <?php
        }
        ?>
        <table>
            <tr>
                <th colspan='5'>Totaal:</th>
            </tr>
            <tr>
                <td colspan='4' style="color:red"><i>Exclusief 21% btw</i></td>
                <td style="color:red"><i>€<?php print $total ?></i></td>
            </tr>
            <tr>
                <td colspan='4' style="color:red">Inclusief 21% btw</td>
                <td style=color:red;><u>€<?php print round($total * 1.21, 2) ?></u></td>
            </tr>
        </table>
    </div>
    <!-- customer data and order button -->
    <div class="order-details">
        <div>
            <h2>Uw gegevens</h2>
            <table>
                <tr>
                    <td>Naam:</td>
                    <td><?php echo $_SESSION["fullname"]; ?></td>
                </tr>
                <tr>
                    <td>Postcode:</td>
                    <td><?php echo $_SESSION["ZIP_code"]; ?> </td>
                </tr>
                <tr>
                    <td>Adres:</td>
                    <td><?php echo $_SESSION["address"]; ?> </td>
                </tr>
                <tr>
                    <td>Huisnummer:</td>
                    <td><?php echo $_SESSION["housenumber"]; ?> </td>
                </tr>
                <tr>
                    <td>Toevoeging:</td>
                    <td><?php echo $_SESSION["addition"]; ?> </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><?php echo $_SESSION["emailaddress"]; ?></td>
                </tr>
                <tr>
                    <td>Woonplaats:</td>
                    <td><?php echo $_SESSION["residence"]; ?></td>
            </table>
        </div>
        <div>
            <?php
            if (count($cart) > 0) {
            ?>
                <a href="Order.php">
                    <button class="button2" style="padding: 5px;">Bestellen</button>
                <?php
            } else {
                ?>
                    <button class="button2" style="padding: 5px;" disabled>Bestellen</button>
                <?php
            }
                ?>
                </a>
                <img class="idealImage" src="Public/Img/ideal.png" alt="">
        </div>
    </div>
    </main>
</body>
<?php
include __DIR__ . "/footer.php";
?>
</html>