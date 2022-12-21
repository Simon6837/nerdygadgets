<?php
include_once "CustomerFunctions.php";

//checks if values are set and if they aren't, it  sets them to empty
$data["E-mail"] = $_POST["E-mail"] ?? "";
$data["naam"] = $_POST["naam"] ?? "";
$data["adres"] = $_POST["adres"] ?? "";
$data["woonplaats"] = $_POST["woonplaats"] ?? "";
$data["huisnummer"] = $_POST["huisnummer"] ?? "";
$data["postcode"] = $_POST["postcode"] ?? "";
$data["huisnummerT"] = $_POST["huisnummerT"] ?? "";

$inputError = array();

$_SESSION['postInfo'] = $_POST;

if (isset($_POST['submit'])){
    $valuesCorrect = true;
    
    if (!emailCheck($data['E-mail'])) {
        $valuesCorrect = false;
        $inputError['email'] = true;
    }
    if (specialCharCheck($data['naam'])){
        $valuesCorrect = false;
        $inputError['naam'] = true;
    }
    if (specialCharCheck($data['adres'])){
        $valuesCorrect = false;
        $inputError['adres'] = true;
    }
    if (specialCharCheck($data['woonplaats'])){
        $valuesCorrect = false;
        $inputError['woonplaats'] = true;
    }
    if (!PostalCodeCheck($data['postcode'])){
        $valuesCorrect = false;
        $inputError['postcode'] = true;
    }
    if (!HuisnummerCheck($data['huisnummer'])) {
        $valuesCorrect = false;
        $inputError['huisnummer'] = true;
    }
    if (!empty($data['huisnummerT'])) {
        if (!ToevoegingCheck($data['huisnummerT'])) {
            $valuesCorrect = false;
            $inputError['huisnummerT'] = true;
        }
    }
    if ($valuesCorrect){
        $script = "<script>window.location = './viewOrder.php';</script>";
        echo $script;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<div class="FormBackground">
    <h1>Voer hier uw gegevens in</h1><br>
    <form method="post" action="CustomerInfo.php">
        <label class="inputTextFormTitleFirst">E-mailadres<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="E-mail" value="<?php print($data["E-mail"]); ?>" placeholder="E-mail" required />
        <?php if (isset($inputError['email'])) { print ("<label class='inputError'><i>Ongeldig e-mailadres</i></label><br>");}?>

        <label class="inputTextFormTitle">Naam<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="naam" value="<?php print($data["naam"]); ?>" placeholder="naam" required />
        <?php if (isset($inputError['naam'])) { print ("<label class='inputError'><i>Speciale karakters in naam zijn niet toegestaan</i></label><br>");}?>

        <label class="inputTextFormTitle">Adres<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="adres" value="<?php print($data["adres"]); ?>" placeholder="adres" required />
        <?php if (isset($inputError['adres'])) { print ("<label class='inputError'><i>Speciale karakters in adres zijn niet toegestaan</i></label><br>");}?>

        <label class="inputTextFormTitle">Huisnummer<label style="color: red">*</label></label>
        <input class="inputTextForm" type="number" name="huisnummer" value="<?php print($data["huisnummer"]); ?>" placeholder="huisnummer" required />
        <?php if (isset($inputError['huisnummer'])) { print ("<label class='inputError'><i>Speciale karakters en letters in huisnummer zijn niet toegestaan</i></label><br>");}?>

        <label class="inputTextFormTitle">Toevoeging</label>
        <input class="inputTextForm" type="text" name="huisnummerT" value="<?php print($data["huisnummerT"]); ?>" placeholder="toevoeging"/>
        <?php if (isset($inputError['huisnummerT'])) { print ("<label class='inputError'><i>Een toevoeging bestaat uit letters</i></label><br>");}?>

        <label class="inputTextFormTitle">Woonplaats<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="woonplaats" value="<?php print($data["woonplaats"]); ?>" placeholder="woonplaats" required />
        <?php if (isset($inputError['woonplaats'])) { print ("<label class='inputError'><i>Speciale karakters in woonplaats zijn niet toegestaan</i></label><br>");}?>

        <label class="inputTextFormTitle">Postcode<label style="color: red">*</label></label>
        <input class="inputTextForm" type="text" name="postcode" value="<?php print($data["postcode"]); ?>" placeholder="postcode" required />
        <?php if (isset($inputError['postcode'])) { print ("<label class='inputError'><i>Postcode voldoet niet aan de standaard vorm</i></label><br>");}?>
        <input class="button2 accountAanmakenTopMargin" type="submit" name="submit" value="submit" formaction="CustomerInfo.php"/>
    </form>

    <br><label>Heeft u een account?</label>

    <form action="Login.php">
        <input class="button2" type="submit" value="Inloggen">
    </form>

    <label class="smallTextDesc">Nog geen account?</label>

    <a class="forceA smallTextDesc" href="AddCustomer.php">Account aanmaken</a>
</div>
</html>