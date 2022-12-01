<!-- de inhoud van dit bestand wordt bovenaan elke pagina geplaatst -->
<?php
session_start();
include "database.php";
$databaseConnection = connectToDatabase();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>NerdyGadgets</title>

    <!-- Javascript -->
    <script src="Public/JS/fontawesome.js"></script>
    <script src="Public/JS/jquery.min.js"></script>
    <script src="Public/JS/bootstrap.min.js"></script>
    <script src="Public/JS/popper.min.js"></script>
    <script src="Public/JS/resizer.js"></script>

    <!-- Style sheets-->
    <link rel="stylesheet" href="Public/CSS/style.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="Public/CSS/typekit.css">
</head>

<body>
    <div class="Background">
        <?php
        include_once('cartModal.php');
        ?>
        <div class="row" id="Header">
            <div class="col-2"><a href="./" id="LogoA">
                    <div id="LogoImage"></div>
                </a></div>
            <div class="col-9" id="CategoriesBar">
                <ul id="ul-class">
                    <?php
                    $HeaderStockGroups = getHeaderStockGroups($databaseConnection);

                    foreach ($HeaderStockGroups as $HeaderStockGroup) {
                    ?>
                        <li>
                            <a href="browse.php?category_id=<?php print $HeaderStockGroup['StockGroupID']; ?>" class="HrefDecoration"><?php print $HeaderStockGroup['StockGroupName']; ?></a>
                        </li>
                    <?php
                    }
                    ?>
                    <li>
                        <a href="categories.php" class="HrefDecoration">Alle categorieën</a>
                    </li>
                </ul>
            </div>
            <!-- code voor US3: zoeken -->
            <div id="Icons">
                <?php 
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    ?>
                    <div class='shoppingcartAmountBubble'><?php print array_sum($_SESSION['cart']) ?></div>
                <?php
                }
                ?>
                
                <ul id="ul-class-navigation">
                    <li>
                        <a href="browse.php" class="HrefDecoration"><i class="fas fa-search red"></i></a>
                    </li>
                    <li id="shoppingCartIcon">
                        <i class="fas fa-shopping-cart red"></i>
                    </li>
                </ul>
                <script>
                    document.querySelector("#shoppingCartIcon").addEventListener("mouseover", function() {
                        document.querySelector("#cartModal").classList.remove("hide");
                    });
                    document.querySelector("#shoppingCartIcon").addEventListener("mouseout", function() {
                        document.querySelector("#cartModal").classList.add("hide");
                    });

                    document.querySelector("#cartModal").addEventListener("mouseover", function() {
                        document.querySelector("#cartModal").classList.remove("hide");
                    });
                    document.querySelector("#cartModal").addEventListener("mouseout", function() {
                        document.querySelector("#cartModal").classList.add("hide");
                    });
                </script>
            </div>

            <!-- einde code voor US3 zoeken -->
        </div>
        <div class="row" id="Content">
            <div class="col-12">
                <div id="SubContent">