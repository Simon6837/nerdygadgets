<?php
//if the item got added to the cart show a message
if (isset($_GET['showAddedMessage'])) {
?>
    <div class="notificationBox">
        <div class="notificationTitle">Gelukt!</div>
        <div class="notificationText"><?php print $StockItem['StockItemName'] . ' is toegevoegd aan de winkelmand' ?></div>
    </div>
<?php
}
if (isset($_GET['showDeletedMessage'])) {
    ?>
        <div class="notificationBox">
            <div class="notificationTitle">Gelukt!</div>
            <div class="notificationText"><?php print $StockItem['StockItemName'] . ' is verwijderd uit de winkelmand' ?></div>
        </div>
    <?php
    }
?>
<script>
    //remove the notification after 3 seconds
    setTimeout(function() {
        $('.notificationBox').fadeOut('slow');
    }, 3000);
    //remove aall parameters from the url
    let url = window.location.href;
    url = url.replace('&showAddedMessage', '');
    url = url.replace('&showDeletedMessage', '');
    url = url.replace('?showAddedMessage', '');
    url = url.replace('?showDeletedMessage', '');
    url = url.replace('=true', '');
    //replace the url without the get parameters but don't reload the page
    window.history.replaceState({}, 'document.title', url);
</script>