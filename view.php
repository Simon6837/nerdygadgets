<?php
include_once __DIR__ . "/header.php";

include_once "cartfuncties.php";
function getVoorraadTekst($actueleVoorraad)
{
    if ($actueleVoorraad > 1000) {
        return "Ruime voorraad beschikbaar.";
    } else {
        return "Voorraad: $actueleVoorraad";
    }
}
$StockItem = getStockItem($_GET['id'], $databaseConnection);
$StockItemImage = getStockItemImage($_GET['id'], $databaseConnection);
?>
<!-- dit bestand bevat alle code voor de pagina die één product laat zien -->
<script>
    // get product temperature by sending a request to the temperature API
    async function getTemperature() {
        let request = await fetch("temperaturefixes.php", {
        }).then((response) => response.json()).then((data) => {
            // display the temperature on the page
            document.getElementById("temperature").innerHTML = `Temperatuur: ${data.data}°C`;
        });
    }
    // only get temperature of cold products
    if (<?php echo $StockItem['IsChillerStock']?> == 1) {
        getTemperature();
        setInterval(getTemperature, 3000);
    }
</script>
<div id="CenteredContent">
    <?php
    if ($StockItem != null) {
    ?>
        <!-- Video -->
        <?php
        if (isset($StockItem['Video'])) {
        ?>
            <div id="VideoFrame">
                <?php print $StockItem['Video']; ?>
            </div>
        <?php }
        ?>
        <div id="ArticleHeader">
            <?php
            if ($StockItemImage) {
                // één plaatje laten zien
                if (count($StockItemImage) == 1) {
            ?>
                    <div id="ImageFrame" style="background-image: url('Public/StockItemIMG/<?php print $StockItemImage[0]['ImagePath']; ?>'); background-size: 300px; background-repeat: no-repeat; background-position: center;"></div>
                <?php
                } else if (count($StockItemImage) >= 2) { ?>
                    <!-- meerdere plaatjes laten zien -->
                    <div id="ImageFrame">
                        <div id="ImageCarousel" class="carousel slide" data-interval="false">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                ?>
                                    <li data-target="#ImageCarousel" data-slide-to="<?php print $i ?>" <?php print(($i == 0) ? 'class="active"' : ''); ?>></li>
                                <?php
                                } ?>
                            </ul>

                            <!-- slideshow -->
                            <div id='carousel' class="carousel-inner">
                                <?php for ($i = 0; $i < count($StockItemImage); $i++) {
                                ?>
                                    <div class="carousel-item <?php print ($i == 0) ? 'active' : ''; ?>">
                                        <img src="Public/StockItemIMG/<?php print $StockItemImage[$i]['ImagePath'] ?>">
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- knoppen 'vorige' en 'volgende' -->
                            <a id="button" class="carousel-control-prev hide" href="#ImageCarousel" data-slide="prev">
                                &#10148;
                            </a>
                            <a id="button2" class="carousel-control-next hide" href="#ImageCarousel" data-slide="next">
                                &#10148;
                            </a>
                            <script>
                                document.querySelector("#ImageFrame").addEventListener("mouseover", function() {
                                    document.querySelector("#button").classList.remove("hide");
                                    document.querySelector("#button2").classList.remove("hide");
                                });

                                document.querySelector("#ImageFrame").addEventListener("mouseout", function() {
                                    document.querySelector("#button").classList.add("hide");
                                    document.querySelector("#button2").classList.add("hide");
                                });
                            </script>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <div id="ImageFrame" style="background-image: url('Public/StockGroupIMG/<?php print $StockItem['BackupImagePath']; ?>'); background-size: cover;"></div>
            <?php
            }
            ?>


            <h1 class="StockItemID">Artikelnummer: <?php print $StockItem["StockItemID"]; ?></h1>
            <h2 class="StockItemNameViewSize StockItemName">
                <?php print $StockItem['StockItemName']; ?>
            </h2>
            <div class="QuantityText"><?php if (intval(substr($StockItem['QuantityOnHand'], 10)) < 1) {
                                            print('Geen voorraad beschikbaar');
                                        } else {
                                            print getVoorraadTekst(substr($StockItem['QuantityOnHand'], 10));
                                        } ?></div>
            <div id="temperature">

            </div>
            <div id="StockItemHeaderLeft">
                <div class="CenterPriceLeft">
                    <div class="CenterPriceLeftChild">
                        <div class="StockItemPriceText"><b><?php print sprintf("€ %.2f", $StockItem['SellPrice']); ?></b></div>
                        <div class="InclBtw"> Inclusief BTW </div>

                        <input class="AmountInput" id="addToCartAmount" min="1" type="number" value="1">
                        <div onclick="addProduct()" class="addProduct">Toevoegen aan winkelwagen</div>
                        <script>
                            //get the amount of the product and add it to the cart
                            function addProduct() {
                                let amount = parseInt(document.getElementById("addToCartAmount").value);
                                //if the item is in the cart add the amount to the total amount
                                let stockInCart = <?php print isset($_SESSION["cart"][$StockItem['StockItemID']]) ? $_SESSION["cart"][$StockItem['StockItemID']] : 0  ?>;
                                //check if the amount is higher than the stock
                                if ((amount + stockInCart) > <?php echo substr($StockItem['QuantityOnHand'], 10) ?>) {
                                    //if the amount is higher than the stock, ask the user if he wants to still add the product to the cart
                                    if (!confirm("Er is niet genoeg voorraad, wilt u het product toch toevoegen aan de winkelwagen?")) {
                                        return;
                                    }
                                }
                                window.location.href = "cart.php?addId=<?php print $_GET['id'] ?>&amount=" + amount;
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <div id="StockItemDescription">
            <h3>Artikel beschrijving</h3>
            <p><?php print $StockItem['SearchDetails']; ?></p>
        </div>
        <!-- deze div kan gebruikt worden voor aanbevolen items -->
        <!-- <div id="StockItemSpecifications"></div> -->
    <?php
    } else {
    ?><h2 id="ProductNotFound">Het opgevraagde product is niet gevonden.</h2><?php
                                                                            } ?>
</div>
<?php
include __DIR__ . "/footer.php";
?>