<?php
include_once __DIR__ . "/header.php";

    $ReturnableResult = null;
    $Sort = "SellPrice";
    $SortName = "price_low_high";

    $AmountOfPages = 0;


    if (isset($_GET['category_id'])) {
        $CategoryID = $_GET['category_id'];
    } else {
        $CategoryID = "";
    }
    if (isset($_GET['products_on_page'])) {
        $ProductsOnPage = $_GET['products_on_page'];
        $_SESSION['products_on_page'] = $_GET['products_on_page'];
    } else if (isset($_SESSION['products_on_page'])) {
        $ProductsOnPage = $_SESSION['products_on_page'];
    } else {
        $ProductsOnPage = 25;
        $_SESSION['products_on_page'] = 25;
    }
    if (isset($_GET['page_number'])) {
        $PageNumber = $_GET['page_number'];
    } else {
        $PageNumber = 0;
    }

    // code deel 1 van User story: Zoeken producten
    // <voeg hier de code in waarin de zoekcriteria worden opgebouwd>
    $SearchString = "";

    if (isset($_GET['search_string'])) {
        $SearchString = $_GET['search_string'];
    }
    if (isset($_GET['sort'])) {
        $SortOnPage = $_GET['sort'];
        $_SESSION["sort"] = $_GET['sort'];
    } else if (isset($_SESSION["sort"])) {
        $SortOnPage = $_SESSION["sort"];
    } else {
        $SortOnPage = "price_low_high";
        $_SESSION["sort"] = "price_low_high";
    }

    switch ($SortOnPage) {
        case "price_high_low": {
                $Sort = "SellPrice DESC";
                break;
            }
        case "name_low_high": {
                $Sort = "StockItemName";
                break;
            }
        case "name_high_low";
            $Sort = "StockItemName DESC";
            break;
        case "price_low_high": {
                $Sort = "SellPrice";
                break;
            }
        default: {
                $Sort = "SellPrice";
                $SortName = "price_low_high";
            }
    }
    $SearchString = str_replace(["'", "\\"], "", $SearchString);
    $searchValues = !empty($SearchString) ? explode(" ", $SearchString) : [];


    // <einde van de code voor zoekcriteria>
    // einde code deel 1 van User story: Zoeken producten


    $Offset = $PageNumber * $ProductsOnPage;

    // code deel 2 van User story: Zoeken producten
    // <voeg hier de code in waarin het zoekresultaat opgehaald wordt uit de database>

    // the query for the search
    $searchQuery = "";
    // the parameters for the query
    $params = [];
    // the parameter types
    $types = "";
    // if a category is selected add it to the query, with a new parameter for the category id and a new parameter type
    if ($CategoryID !== "") {
        $searchQuery = "JOIN stockitemstockgroups USING(StockItemID) JOIN stockgroups ON stockitemstockgroups.StockGroupID = stockgroups.StockGroupID WHERE ? IN (SELECT StockGroupID from stockitemstockgroups WHERE StockItemID = SI.StockItemID) ";
        $types .= "i";
        $params[] = $CategoryID;
    }

    // if a search string is given add it to the query, with a new parameter for the search string and a new parameter type
    if (!empty($_GET['search_string'])) {
        if ($searchQuery != "") {
            $searchQuery .= "AND ";
        } else {
            $searchQuery .= "WHERE ";
        }
        // the * is added to the search string to make it a wildcard search, meaning for example "chocolate" will also match "chocolates"
        for ($i = 0; $i < count($searchValues); $i++) {
            $searchValues[$i] .= "*";
        }
        // convert the search values array into a string to be used in the match statement (with commas between the values)
        $matchString = implode(", ", $searchValues);
        $searchQuery .= "MATCH(tags, searchdetails, MarketingComments) AGAINST (? IN BOOLEAN MODE)";
        $types .= "s";
        $params[] = $matchString;
    }
    // add params and types for the limit and offset
    array_push($params, $ProductsOnPage, $Offset);
    $types .= "ii";
    // build the query
    $Query = "SELECT SI.StockItemID, SI.StockItemName, SI.MarketingComments, TaxRate, RecommendedRetailPrice, ROUND(TaxRate * RecommendedRetailPrice / 100 + RecommendedRetailPrice,2) as SellPrice, QuantityOnHand, (SELECT ImagePath FROM stockitemimages WHERE StockItemID = SI.StockItemID LIMIT 1) as ImagePath, (SELECT ImagePath FROM stockgroups JOIN stockitemstockgroups USING(StockGroupID) WHERE StockItemID = SI.StockItemID LIMIT 1) as BackupImagePath FROM stockitems SI JOIN stockitemholdings SIH USING(stockitemid) " . $searchQuery .  " GROUP BY StockItemID ORDER BY " . $Sort . " LIMIT ? OFFSET ? ";
    $Statement = mysqli_prepare($databaseConnection, $Query);
    mysqli_stmt_bind_param($Statement, $types, ...$params);
    mysqli_stmt_execute($Statement);

    $ReturnableResult = mysqli_stmt_get_result($Statement);
    $ReturnableResult = mysqli_fetch_all($ReturnableResult, MYSQLI_ASSOC);

    // get the amount of pages with a query that only returns the amount of products
    $Query = "
            SELECT count(DISTINCT StockItemID)
            FROM stockitems SI
            $searchQuery
            ";
    $types = substr($types, 0, -2);
    array_pop($params);
    array_pop($params);
    $Statement = mysqli_prepare($databaseConnection, $Query);
    if (!empty($params)) {
        mysqli_stmt_bind_param($Statement, $types, ...$params);
    }
    mysqli_stmt_execute($Statement);
    $Result = mysqli_stmt_get_result($Statement);
    $Result = mysqli_fetch_all($Result, MYSQLI_ASSOC);


    // <einde van de code voor zoekresultaat>
    // einde deel 2 van User story: Zoeken producten
    $amount = $Result[0];
    if (isset($amount)) {
        $AmountOfPages = ceil($amount["count(DISTINCT StockItemID)"] / $ProductsOnPage);
    }

    function getVoorraadTekst($actueleVoorraad)
    {
        if ($actueleVoorraad > 1000) {
            return "Ruime voorraad beschikbaar.";
        } else {
            return "Voorraad: $actueleVoorraad";
        }
    }
    function berekenVerkoopPrijs($adviesPrijs, $btw)
    {
        return $btw * $adviesPrijs / 100 + $adviesPrijs;
    }
    ?>
    <!-- dit bestand bevat alle code voor het productoverzicht -->
    <!-- code deel 3 van User story: Zoeken producten : de html -->
    <!-- de zoekbalk links op de pagina  -->

    <div id="FilterFrame">
        <h2 class="FilterText"><i class="fas fa-filter"></i> Filteren </h2>
        <form>
            <div id="FilterOptions">
                <h4 class="FilterTopMargin"><i class="fas fa-search"></i> Zoeken</h4>
                <input type="text" name="search_string" id="search_string" value="<?php print (isset($_GET['search_string'])) ? $_GET['search_string'] : ""; ?>" class="form-submit">
                <h4 class="FilterTopMargin"><i class="fas fa-list-ol"></i> Aantal producten op pagina</h4>

                <input type="hidden" name="category_id" id="category_id" value="<?php print (isset($_GET['category_id'])) ? $_GET['category_id'] : ""; ?>">
                <select name="products_on_page" id="products_on_page" onchange="this.form.submit()">>
                    <option value="25" <?php if ($_SESSION['products_on_page'] == 25) {
                                            print "selected";
                                        } ?>>25
                    </option>
                    <option value="50" <?php if ($_SESSION['products_on_page'] == 50) {
                                            print "selected";
                                        } ?>>50
                    </option>
                    <option value="75" <?php if ($_SESSION['products_on_page'] == 75) {
                                            print "selected";
                                        } ?>>75
                    </option>
                </select>
                <h4 class="FilterTopMargin"><i class="fas fa-sort"></i> Sorteren</h4>
                <select name="sort" id="sort" onchange="this.form.submit()">>
                    <option value="price_low_high" <?php if ($_SESSION['sort'] == "price_low_high") {
                                                        print "selected";
                                                    } ?>>Prijs oplopend
                    </option>
                    <option value="price_high_low" <?php if ($_SESSION['sort'] == "price_high_low") {
                                                        print "selected";
                                                    } ?>>Prijs aflopend
                    </option>
                    <option value="name_low_high" <?php if ($_SESSION['sort'] == "name_low_high") {
                                                        print "selected";
                                                    } ?>>Naam oplopend
                    </option>
                    <option value="name_high_low" <?php if ($_SESSION['sort'] == "name_high_low") {
                                                        print "selected";
                                                    } ?>>Naam aflopend
                    </option>
                </select>
        </form>
    </div>
    </div>

    <!-- einde zoekresultaten die links van de zoekbalk staan -->
    <!-- einde code deel 3 van User story: Zoeken producten  -->

    <div id="ResultsArea" class="Browse">
        <?php
        if (isset($ReturnableResult) && count($ReturnableResult) > 0) {
            foreach ($ReturnableResult as $row) {
        ?>
                <!--  coderegel 1 van User story: bekijken producten  -->

                <div class="ListItem" onclick="window.location.replace('view.php?id=<?php print $row['StockItemID']; ?>')">

                    <!-- einde coderegel 1 van User story: bekijken producten   -->
                    <div id="ProductFrame">
                        <?php
                        if (isset($row['ImagePath'])) { ?>
                            <div class="ImgFrame" style="background-image: url('<?php print "Public/StockItemIMG/" . $row['ImagePath']; ?>'); background-size: 230px; background-repeat: no-repeat; background-position: center;"></div>
                        <?php } else if (isset($row['BackupImagePath'])) { ?>
                            <div class="ImgFrame" style="background-image: url('<?php print "Public/StockGroupIMG/" . $row['BackupImagePath'] ?>'); background-size: cover;"></div>
                        <?php }
                        ?>

                        <div id="StockItemFrameRight">
                            <div class="CenterPriceLeftChild">
                                <h1 class="StockItemPriceText">€<?php print sprintf(" %0.2f", berekenVerkoopPrijs($row["RecommendedRetailPrice"], $row["TaxRate"])); ?></h1>
                                <h6>Inclusief BTW </h6>
                            </div>
                            <a href="cart.php?addId=<?php print $row['StockItemID']; ?>" class="shoppingCartProductIcon">
                                <i class="fas fa-cart-plus red cart-icon"></i>
                            </a>
                        </div>
                        <h1 class="StockItemID">Artikelnummer: <?php print $row["StockItemID"]; ?></h1>
                        <p class="StockItemName"><?php print $row["StockItemName"]; ?></p>
                        <p class="StockItemComments"><?php print $row["MarketingComments"]; ?></p>
                        <h4 class="ItemQuantity"> <?php if (intval($row['QuantityOnHand']) < 1) {
                                                        print('Geen voorraad beschikbaar');
                                                    } else {
                                                        print getVoorraadTekst($row['QuantityOnHand']);
                                                    } ?>
                        </h4>
                    </div>
                    <!--  coderegel 2 van User story: bekijken producten  -->

                </div>

                <!--  einde coderegel 2 van User story: bekijken producten  -->
            <?php } ?>

            <form id="PageSelector">

                <!-- code deel 4 van User story: Zoeken producten  -->

                <input type="hidden" name="search_string" id="search_string" value="<?php if (isset($_GET['search_string'])) {
                                                                                        print($_GET['search_string']);
                                                                                    } ?>">
                <input type="hidden" name="sort" id="sort" value="<?php print($_SESSION['sort']); ?>">

                <!-- einde code deel 4 van User story: Zoeken producten  -->
                <input type="hidden" name="category_id" id="category_id" value="<?php if (isset($_GET['category_id'])) {
                                                                                    print($_GET['category_id']);
                                                                                } ?>">
                <input type="hidden" name="result_page_numbers" id="result_page_numbers" value="<?php print (isset($_GET['result_page_numbers'])) ? $_GET['result_page_numbers'] : "0"; ?>">
                <input type="hidden" name="products_on_page" id="products_on_page" value="<?php print($_SESSION['products_on_page']); ?>">

                <?php
                if ($AmountOfPages > 0) {
                    for ($i = 1; $i <= $AmountOfPages; $i++) {
                        if ($PageNumber == ($i - 1)) {
                ?>
                            <div id="SelectedPage" style="color:#fff;"><?php print $i; ?></div><?php
                                                                                            } else { ?>
                            <button id="page_number" class="PageNumber" value="<?php print($i - 1); ?>" type="submit" name="page_number"><?php print($i); ?></button>
                <?php }
                                                                                        }
                                                                                    }
                ?>
            </form>
        <?php
        } else {
        ?>
            <h2 id="NoSearchResults">
                Yarr, er zijn geen resultaten gevonden.
            </h2>
        <?php
        }
        ?>
    </div>

    <?php
    include __DIR__ . "/footer.php";
    ?>