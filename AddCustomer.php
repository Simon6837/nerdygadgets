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


// if button is pressed checks if all input values correspond with set restrictions.
if (isset($_POST['toevoegen'])){
    $uppercase = preg_match('@[A-Z]@', $data['WW']);
    $lowercase = preg_match('@[a-z]@', $data['WW']);
    $number    = preg_match('@[0-9]@', $data['WW']);
    $specialChars = preg_match('@[^\w]@', $data['WW']);
    $valuesCorrect = true;
    
    if (!(str_contains($data['E-mail'], '@') and strlen($data['E-mail'] >= 6 and str_contains((substr($data['E-mail'], -5)), '.')))) {
        print ('e-mailadres voldoet niet <br>'); $valuesCorrect = false;
    }
    if ((!(2 < strlen($data['Gbrnaam']) && strlen($data['Gbrnaam']) <21) || str_contains($data['Gbrnaam'], '@'))){
        print ('gebruikersnaam voldoet niet aan de eisen <br>');  $valuesCorrect = false;
    }
    if ((!$uppercase || !$lowercase || !$number || !$specialChars || strlen($data['WW'] <= 12))){
        print ('wachtwoord voldoet niet aan de eisen <br>');  $valuesCorrect = false;
    }
    if (specialChar($data['naam'])){
        print ('speciale karakters in naam zijn niet toegestaan <br>'); $valuesCorrect = false;
    }
    if (specialChar($data['adres'])){
        print ('speciale karakters in adres zijn niet toegestaan <br>'); $valuesCorrect = false;
    }
    if (specialChar($data['woonplaats'])){
        print ('speciale karakters in woonplaats zijn niet toegestaan <br>'); $valuesCorrect = false;
    }
    if (!($data['WW'] == $data['testWW'])){
        print ('wachtwoorden komen niet overeen'); $valuesCorrect = false;
    }
    //checks if given values already exist within the database and sends a message when they already exist
    if(mysqli_num_rows(mysqli_query($databaseConnection, "SELECT logonname FROM People WHERE logonname = '". $_POST['Gbrnaam']."'"))){
        print('Deze gebruikersnaam bestaat al'); $valuesCorrect = false;
    }
    if(mysqli_num_rows(mysqli_query($databaseConnection, "SELECT emailaddress FROM People WHERE emailaddress = '". $_POST['E-mail']."'"))){
        print('Deze gebruikersnaam bestaat al'); $valuesCorrect = false;
    }
    if ($valuesCorrect) {
        //adds the given values to the database if all requirements have been met.
        $data["melding"] = klantGegevensToevoegen($data) ? 'Account is succesvol aangemaakt!' : 'Account is niet aangemaakt!';
        sleep(2);
        header('location: http://localhost/nerdygadgets/login.php');
    }
}
?>



<h1>Account aanmaken</h1><br><br>
<form method="post" action="AddCustomer.php">
    <label>E-mailadres<label style="color: red" >*</label></label>
    <input type="text" name="E-mail" value="<?php print($data["E-mail"]); ?>" placeholder="e-mailadres" required/>
    <label>Gebruikersnaam</label>
    <input type="text" name="Gbrnaam" value="<?php print($data["Gbrnaam"]); ?>" placeholder="gebruikersnaam"/>
    <label>Wachtwoord<label style="color: red" >*</label></label>
    <input type="password" name="WW" value="<?php print($data["WW"]); ?>" placeholder="wachtwoord" required>
    <label>herhaal wachtwoord<label style="color: red" >*</label></label>
    <input type="password" name="testWW" value="<?php print($data["testWW"]); ?>" placeholder="herhaal wachtwoord" required>
    <p style="color:black;font-size:10px;">Moet bevatten: <i>Hoofdletter, kleine letter, speciaal karakter, getal</i></p>
    <label>Naam<label style="color: red" >*</label></label>
    <input type="text" name="naam" value="<?php print($data["naam"]); ?>" placeholder="naam" required/>
    <label>Adres<label style="color: red" >*</label></label>
    <input type="text" name="adres" value="<?php print($data["adres"]); ?>" placeholder="adres" required/>
    <label>Woonplaats<label style="color: red" >*</label></label>
    <input type="text" name="woonplaats" value="<?php print($data["woonplaats"]); ?>" placeholder="woonplaats" required/>
    <input type="submit" name="toevoegen" value="Toevoegen" />
</form>
<br><?php print($data["melding"]); ?><br>
</body>
</html>
