<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Inloggen</title></head>
<body>
<?php
include 'CustomerFunctions.php';
$databaseConnection = connectToDatabase();

//checks if values are set and if they aren't, it  sets them to empty

$data["E-mail"] = isset($_POST["E-mail"]) ? $_POST["E-mail"] : "";
$data["Gbrnaam"] = isset($_POST["Gbrnaam"]) ? $_POST["Gbrnaam"] : "";
$data["WW"] = isset($_POST["WW"]) ? $_POST["WW"] : "";
$data["testWW"] = isset($_POST["testWW"]) ? $_POST["testWW"] : "";
$data["naam"] = isset($_POST["naam"]) ? $_POST["naam"] : "";
$data["adres"] = isset($_POST["adres"]) ? $_POST["adres"] : "";
$data["woonplaats"] = isset($_POST["woonplaats"]) ? $_POST["woonplaats"] : "";
$data["melding"] = "";
//hashes password and saves it in array
$hashedpassword = password_hash($data['WW'], PASSWORD_BCRYPT);
$data['hWW'] = $hashedpassword;

//Checks is input meets the set requirements
$inputError = array();

// if button is pressed checks if all input values correspond with set restrictions.
if (isset($_POST['toevoegen'])){
    $uppercase = preg_match('@[A-Z]@', $data['WW']);
    $lowercase = preg_match('@[a-z]@', $data['WW']);
    $number    = preg_match('@[0-9]@', $data['WW']);
    $specialChars = preg_match('@[^\w]@', $data['WW']);
    $valuesCorrect = true;
    
    if (!(str_contains($data['E-mail'], '@') and strlen($data['E-mail'] >= 6 and str_contains((substr($data['E-mail'], -5)), '.')))) {
        $error[] = 'ongeldig e-mailadres'; $valuesCorrect = false;
        $inputError['email'] = 'error';
    }
    if ((!(2 < strlen($data['Gbrnaam']) && strlen($data['Gbrnaam']) <21) || str_contains($data['Gbrnaam'], '@'))){
        $error[] = 'gebruikersnaam voldoet niet aan de eisen';  $valuesCorrect = false;
        $inputError['gebruikersnaam'] = 'error';
    }
    if ((!$uppercase || !$lowercase || !$number || !$specialChars || strlen($data['WW'] <= 12))){
        $error[] = 'wachtwoord voldoet niet aan de eisen';  $valuesCorrect = false;
        $inputError['wachtwoord'] = 'error';
    }
    if (specialChar($data['naam'])){
        $error[] = 'speciale karakters in naam zijn niet toegestaan'; $valuesCorrect = false;
        $inputError['naam'] = 'error';
    }
    if (specialChar($data['adres'])){
        $error[] = 'speciale karakters in adres zijn niet toegestaan'; $valuesCorrect = false;
        $inputError['adres'] = 'error';
    }
    if (specialChar($data['woonplaats'])){
        $valuesCorrect = false;
        $inputError['woonplaats'] = 'error';
    }
    if (!($data['WW'] == $data['testWW'])){
        $valuesCorrect = false;
        $inputError['herhaalwachtwoord'] = 'error';
    }
    //checks if given values already exist within the database and sends a message when they already exist
    if(mysqli_num_rows(mysqli_query($databaseConnection, "SELECT logonname FROM People WHERE logonname = '". $_POST['Gbrnaam']."'"))){
        $inputError['gebruikersnaamExists'] = 'error'; $valuesCorrect = false;
    }
    if(mysqli_num_rows(mysqli_query($databaseConnection, "SELECT emailaddress FROM People WHERE emailaddress = '". $_POST['E-mail']."'"))){
        $inputError['emailExists'] = 'error'; $valuesCorrect = false;
    }
    if ($valuesCorrect) {
        //adds the given values to the database if all requirements have been met.
        $data["melding"] = klantGegevensToevoegen($data) ? 'Account is succesvol aangemaakt!' : 'Account is niet aangemaakt!';
        sleep(2);
        header('location: http://localhost/nerdygadgets/login.php');
    }
}
?>

<div class="FormBackground">
<h1>Account aanmaken</h1><br>
<form method="post" action="AddCustomer.php">
    <label class="inputTextFormTitleFirst">E-mailadres<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="E-mail" value="<?php print($data["E-mail"]); ?>" placeholder="e-mailadres" required/>
    <?php if (array_key_exists('email', $inputError)) { print ("<label class='inputError'><i>Ongeldig e-mailadres</i></label><br>");}?>
    <?php if (array_key_exists('emailExists', $inputError)) { print ("<label class='inputError'><i>Er bestaat al een account met dit e-mailadres</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Gebruikersnaam<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="Gbrnaam" value="<?php print($data["Gbrnaam"]); ?>" placeholder="gebruikersnaam"/>
    <label class="smallTextDesc"><i>Aantal karakters: 3-20, mag geen '@' bevatten</i></label><br>
    <?php if (array_key_exists('gebruikersnaam', $inputError)) { print ("<label class='inputError'><i>Gebruikernaam voldoet niet aan de eisen</i></label><br>");}?>
    <?php if (array_key_exists('gebruikersnaamExists', $inputError)) { print ("<label class='inputError'><i>Gebruikersnaam bestaat al</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Wachtwoord<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="password" name="WW" value="<?php print($data["WW"]); ?>" placeholder="wachtwoord" required>
    <label class="smallTextDesc"><i>Moet bevatten: Hoofdletter, kleine letter, speciaal karakter, getal</i></label><br>
    <?php if (array_key_exists('wachtwoord', $inputError)) { print ("<label class='inputError'><i>Wachtwoord voldoet niet aan de eisen</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Herhaal wachtwoord<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="password" name="testWW" value="<?php print($data["testWW"]); ?>" placeholder="herhaal wachtwoord" required>
    <?php if (array_key_exists('herhaalwachtwoord', $inputError)) { print ("<label class='inputError'><i>Wachtwoorden komen niet overeen</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Naam<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="naam" value="<?php print($data["naam"]); ?>" placeholder="naam" required/>
    <?php if (array_key_exists('naam', $inputError)) { print ("<label class='inputError'><i>Speciale karakters in naam zijn niet toegestaan</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Adres<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="adres" value="<?php print($data["adres"]); ?>" placeholder="adres" required/>
    <?php if (array_key_exists('adres', $inputError)) { print ("<label class='inputError'><i>Speciale karakters in adres zijn niet toegestaan</i></label><br>");}?>
    
    <label class="inputTextFormTitle">Woonplaats<label style="color: red" >*</label></label>
    <input class="inputTextForm" type="text" name="woonplaats" value="<?php print($data["woonplaats"]); ?>" placeholder="woonplaats" required/>
    <?php if (array_key_exists('woonplaats', $inputError)) { print ("<label class='inputError'><i>Speciale karakters in woonplaats zijn niet toegestaan</i></label><br>");}?>
    <input class="button2 accountAanmakenTopMargin" type="submit" name="toevoegen" value="Account aanmaken" />
</form>
</div>
</body>
</html>
