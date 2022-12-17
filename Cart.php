<?php
include __DIR__ . "/header.php";
include_once "cartfuncties.php";
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Winkelwagen</title>
</head>

<body>
    <table class="shoppingCartTable" id="cart">
        <tr>
            <th colspan="5">
                <h1 style="text-align: center; margin-bottom:10px;">Inhoud Winkelwagen</h1>
            </th>
        </tr>
        <?php
        $total = 0;
        $cart = getCart();
        //if the cart is empty show it. and prevent running the rest of the code
        if (empty($cart)) {
            echo "<tr style='text-align: center'><td colspan='5'>Uw winkelwagen is leeg</td></tr>";
            include __DIR__ . "/footer.php";
            return;
        }
        ?>
        <tr class="tableLine" class="cart">
            <th colspan="2">Product</th>
            <th style="text-align:center;">Aantal</th>
            <th style="text-align:center;">Prijs per stuk</th>
            <th style="text-align:center;">Totaal prijs</th>
        </tr>
        <?php
        //loop through the cart
        foreach ($cart as $key => $item) {
            //get the item
            $StockItem = getStockItem($key, $databaseConnection);
            //remove btw because milan wants to
            $exPrice = round($StockItem['SellPrice'] / 121 * 100, 2);
            //calculate the total price
            $total += $exPrice * $item;
            //set the image
            $imagepath = ($stockItemImage = getStockItemImage($key, $databaseConnection)) ? "Public/StockItemIMG/" . $stockItemImage[0]['ImagePath'] : "Public/StockGroupIMG/" . $StockItem['BackupImagePath'];
            //show the item in a table row
        ?>
            <tr class="tableLine">
                <td><img onclick="window.location.replace('view.php?id=<?php print $key ?>')" style='width: 100px; cursor: pointer;' src='<?php print $imagepath; ?>'></td>
                <td><a class="cartName" href='view.php?id=<?php print $key ?>'><?php print $StockItem['StockItemName'] ?></a> <br> <a href='cart.php?deleteId=<?php print $key ?>'>Verwijder</a></td>
                <!-- change the product amount -->
                <td style="text-align:center;" class="itemCartCount"><input class="AmountInput" id="setProductAmount<?php print $key?>" type="number" value="<?php print $item ?>"></td>
                <script>
                    //add an event listener to the input field that changes the amount of the product when the value changes
                    document.getElementById("setProductAmount<?php print $key?>").addEventListener("change", function() {
                        //check if the amount is higher than the stock
                        if (this.value > <?php echo substr($StockItem['QuantityOnHand'], 10) ?>) {
                                    //if the amount is higher than the stock, ask the user if he wants to still add the product to the cart
                                    if (!confirm("Er is niet genoeg voorraad, wilt u het product toch toevoegen aan de winkelwagen?")) {
                                        return;
                                    }
                                }

                        window.location.replace("cart.php?setAmountId=<?php print $key ?>&amount=" + this.value);
                    });
                </script>
                <!-- uncomment to bring back the - and + buttons -->
                <!-- <td style="text-align:center;" class="itemCartCount"><a href='cart.php?removeId=<?php print $key ?>'><b>-</b> </a><?php print $item ?><a href='cart.php?addId=<?php print $key ?>'><b>+</b> </a></td> -->
                <td style="text-align:center;color:red"><i>€<?php print $exPrice ?></i></td>
                <td style="text-align:center;color:red"><i>€<?php print $exPrice * $item ?></i></td>
            </tr>
        <?php
        }
        ?>
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
        <tr style='text-align: right;'>
            <!-- <td colspan='5'><a href='https://www.ideal.nl/demo/en/?screens=dskweb&bank=rabo&type=dsk'>Bestellen</a></td> -->
            <td colspan="5">
                <?php if (isset($_SESSION['loggedInUserId'])) : ?>
                    <form action="viewOrder.php">
                        <input class="button2" type="submit" value="Bestellen met ideal">
                    </form>
                <?php else : ?>
                    <form action="CustomerInfo.php">
                        <input class="button2" type="submit" value="Bestellen met ideal">
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <p class="shopFurther">
        <?php if (isset($_GET['returnId'])) : ?>
            <a href='view.php?id=<?php print($_GET['returnId']) ?>'>Verder met winkelen</a>
        <?php else : ?>
            <a href='index.php'>Verder met winkelen</a>
        <?php endif; ?>
    </p>
    <?php
    include __DIR__ . "/footer.php";
    ?>