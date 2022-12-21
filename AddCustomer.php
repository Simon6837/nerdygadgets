<?php
include_once 'CustomerFunctions.php';
$databaseConnection = connectToDatabase();

//checks if values are set and if they aren't, it  sets them to empty

$data["E-mail"] = isset($_POST["E-mail"]) ? $_POST["E-mail"] : "";
$data["Gbrnaam"] = isset($_POST["Gbrnaam"]) ? $_POST["Gbrnaam"] : "";
$data["WW"] = isset($_POST["WW"]) ? $_POST["WW"] : "";
$data["testWW"] = isset($_POST["testWW"]) ? $_POST["testWW"] : "";
$data["naam"] = isset($_POST["naam"]) ? $_POST["naam"] : "";
$data["adres"] = isset($_POST["adres"]) ? $_POST["adres"] : "";
$data["woonplaats"] = isset($_POST["woonplaats"]) ? $_POST["woonplaats"] : "";
$data["huisnummer"] = $_POST["huisnummer"] ?? "";
$data["postcode"] = $_POST["postcode"] ?? "";
$data["huisnummerT"] = $_POST["huisnummerT"] ?? "";
$data["melding"] = "";
//hashes password and saves it in array
$hashedpassword = password_hash($data['WW'], PASSWORD_BCRYPT);
$data['hWW'] = $hashedpassword;

//Checks is input meets the set requirements
$inputError = array();

// if button is pressed checks if all input values correspond with set restrictions.
if (isset($_POST['toevoegen'])){
    $valuesCorrect = true;
    
    if (!emailCheck($data['E-mail'])) {
        $valuesCorrect = false;
        $inputError['email'] = true;
    }
    if (!usernameCheck($data['Gbrnaam'])){
        $valuesCorrect = false;
        $inputError['gebruikersnaam'] = true;
    }
    if (!passwordCheck($data['WW'])){
        $valuesCorrect = false;
        $inputError['wachtwoord'] = true;
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
    if (!($data['WW'] == $data['testWW'])){
        $valuesCorrect = false;
        $inputError['herhaalwachtwoord'] = true;
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
    //checks if given values already exist within the database and sends a message when they already exist
    if(mysqli_num_rows(mysqli_query($databaseConnection, "SELECT logonname FROM People WHERE logonname = '". $_POST['Gbrnaam']."'"))){
        $inputError['gebruikersnaamExists'] = true; $valuesCorrect = false;
    }
    if(mysqli_num_rows(mysqli_query($databaseConnection, "SELECT emailaddress FROM People WHERE emailaddress = '". $_POST['E-mail']."'"))){
        $inputError['emailExists'] = true; $valuesCorrect = false;
    }
    if ($valuesCorrect) {
        //adds the given values to the database if all requirements have been met.
        $data["melding"] = klantGegevensToevoegen($data) ? 'Account is succesvol aangemaakt!' : 'Account is niet aangemaakt!';
        sleep(2);
        $script = "<script>window.location = './Login.php';</script>";
        echo $script;
    }
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Inloggen</title></head>
<body>
<div class="FormBackground">
<h1>Account aanmaken</h1><br>
<form method="post" action="AddCustomer.php">
    <label class="inputTextFormTitleFirst">E-mailadres<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="E-mail" value="<?php print($data["E-mail"]); ?>" placeholder="e-mailadres" required/>
    <?php if (isset($inputError['email'])) { print ("<label class='inputError'><i>Ongeldig e-mailadres</i></label><br>");}?>
    <?php if (isset($inputError['emailExists'])) { print ("<label class='inputError'><i>Er bestaat al een account met dit e-mailadres</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Gebruikersnaam<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="Gbrnaam" value="<?php print($data["Gbrnaam"]); ?>" placeholder="gebruikersnaam"/>
    <label class="smallTextDesc"><i>Aantal karakters: 3-20, mag geen '@' bevatten</i></label><br>
    <?php if (isset($inputError['gebruikersnaam'])) { print ("<label class='inputError'><i>Gebruikernaam voldoet niet aan de eisen</i></label><br>");}?>
    <?php if (isset($inputError['gebruikersnaamExists'])) { print ("<label class='inputError'><i>Gebruikersnaam bestaat al</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Wachtwoord<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="password" name="WW" value="<?php print($data["WW"]); ?>" placeholder="wachtwoord" required>
    <label class="smallTextDesc"><i>Moet bevatten: Hoofdletter, kleine letter, speciaal karakter, getal</i></label><br>
    <?php if (isset($inputError['wachtwoord'])) { print ("<label class='inputError'><i>Wachtwoord voldoet niet aan de eisen</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Herhaal wachtwoord<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="password" name="testWW" value="<?php print($data["testWW"]); ?>" placeholder="herhaal wachtwoord" required>
    <?php if (isset($inputError['herhaalwachtwoord'])) { print ("<label class='inputError'><i>Wachtwoorden komen niet overeen</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Naam<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="naam" value="<?php print($data["naam"]); ?>" placeholder="naam" required/>
    <?php if (isset($inputError['naam'])) { print ("<label class='inputError'><i>Speciale karakters in naam zijn niet toegestaan</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Adres<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="adres" value="<?php print($data["adres"]); ?>" placeholder="adres" required/>
    <?php if (isset($inputError['adres'])) { print ("<label class='inputError'><i>Speciale karakters in adres zijn niet toegestaan</i></label><br>");}?>

    <label class="inputTextFormTitle">Huisnummer<label style="color: red">*</label></label>
    <input class="inputTextForm" type="number" name="huisnummer" value="<?php print($data["huisnummer"]); ?>" placeholder="huisnummer" required />
    <?php if (isset($inputError['huisnummer'])) { print ("<label class='inputError'><i>Speciale karakters en letters in huisnummer zijn niet toegestaan</i></label><br>");}?>

    <label class="inputTextFormTitle">Toevoeging</label>
    <input class="inputTextForm" type="text" name="huisnummerT" value="<?php print($data["huisnummerT"]); ?>" placeholder="toevoeging"/>
    <?php if (isset($inputError['huisnummerT'])) { print ("<label class='inputError'><i>Een toevoeging bestaat uit letters</i></label><br>");}?>

    <label class="inputTextFormTitle">Woonplaats<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="woonplaats" value="<?php print($data["woonplaats"]); ?>" placeholder="woonplaats" required/>
    <?php if (isset($inputError['woonplaats'])) { print ("<label class='inputError'><i>Speciale karakters in woonplaats zijn niet toegestaan</i></label><br>");}?>

    <label class="inputTextFormTitle">Postcode<label style="color: red">*</label></label>
    <input class="inputTextForm" type="text" name="postcode" value="<?php print($data["postcode"]); ?>" placeholder="postcode" required />
    <?php if (isset($inputError['postcode'])) { print ("<label class='inputError'><i>Postcode voldoet niet aan de standaard vorm</i></label><br>");}?>
    <input class="button2 accountAanmakenTopMargin" type="submit" name="toevoegen" value="Account aanmaken" />
</form>
</div>
</body>
</html>
