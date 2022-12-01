<?php
//if the item got added to the cart show a message
if (isset($_GET['showSuccessMessage'])) {
?>
    <div class="notificationBox">
        <div class="notificationTitle">Gelukt!!</div>
        <div class="notificationText"><?php print $StockItem['StockItemName'] . ' is toegevoegd aan de winkelmand' ?></div>
    </div>
<?php
}
if (isset($_GET['showErrorMessage'])) {
    ?>
        <div class="notificationBox" style="background: red">
            <div class="notificationTitle">Gelukt!!</div>
            <div class="notificationText"><?php print $StockItem['StockItemName'] . ' is toegevoegd aan de winkelmand' ?></div>
        </div>
    <?php
    }
?>