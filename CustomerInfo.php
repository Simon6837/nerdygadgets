<!DOCTYPE html>
<html lang="nl">

<?php
include __DIR__ . "/header.php";

//checks if values are set and if they aren't, it  sets them to empty
$data["E-mail"] = isset($_POST["E-mail"]) ? $_POST["E-mail"] : "";
$data["naam"] = isset($_POST["naam"]) ? $_POST["naam"] : "";
$data["adres"] = isset($_POST["adres"]) ? $_POST["adres"] : "";
$data["woonplaats"] = isset($_POST["woonplaats"]) ? $_POST["woonplaats"] : "";
?>

<div class="FormBackground">
    <h1>Voer hier uw gegevens in</h1><br>
    <form action="viewOrder.php" method="POST">
        <label class="inputTextFormTitleFirst">E-mailadres<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="E-mail" value="<?php print($data["E-mail"]); ?>" required />
        <label class="inputTextFormTitle">Naam<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="naam" value="<?php print($data["naam"]); ?>" required />
        <label class="inputTextFormTitle">Adres<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="adres" value="<?php print($data["adres"]); ?>" required />
        <label class="inputTextFormTitle">Woonplaats<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="woonplaats" value="<?php print($data["woonplaats"]); ?>" required />
        <input class="button2 accountAanmakenTopMargin" type="submit" name="toevoegen" value="Verder" />
    </form>

    <br><label>Heeft u een account?</label>

    <form action="Login.php">
        <input class="button2" type="submit" value="Inloggen">
    </form>

    <label class="smallTextDesc">Nog geen account?</label>

    <a class="forceA smallTextDesc" href="AddCustomer.php">Account aanmaken</a>
</div>