<?php
include_once __DIR__ . "/header.php";
include_once 'CustomerFunctions.php';
$databaseConnection = connectToDatabase();
if (isset($_SESSION['userdata']['loggedInUserId'])) {
    $nameDatabase = mysqli_prepare($databaseConnection, "SELECT logonname, EmailAddress, residence, address, Housenumber, Addition, ZIP_code FROM people WHERE personid = " . $_SESSION['userdata']['loggedInUserId'] . "");
    mysqli_stmt_execute($nameDatabase);
    $nameResult = mysqli_stmt_get_result($nameDatabase);
    $name = mysqli_fetch_all($nameResult, MYSQLI_ASSOC);

    $data["editE-mail"] = isset($_POST["editE-mail"]) ? $_POST["editE-mail"] : "";
    $data["editGbrnaam"] = isset($_POST["editGbrnaam"]) ? $_POST["editGbrnaam"] : "";
    $data["editadres"] = isset($_POST["editadres"]) ? $_POST["editadres"] : "";
    $data["editwoonplaats"] = isset($_POST["editwoonplaats"]) ? $_POST["editwoonplaats"] : "";
    $data["edithuisnummer"] = $_POST["edithuisnummer"] ?? "";
    $data["editpostcode"] = $_POST["editpostcode"] ?? "";
    $data["edithuisnummerT"] = $_POST["edithuisnummerT"] ?? "";
    //Checks is input meets the set requirements
    $inputError = array();
    if (isset($_POST['accountGegevensAanpassen'])) {
        $valuesCorrect = true;

        if (!emailCheck($data['editE-mail'])) {
            $valuesCorrect = false;
            $inputError['email'] = true;
        }
        if (!usernameCheck($data['editGbrnaam'])) {
            $valuesCorrect = false;
            $inputError['gebruikersnaam'] = true;
        }
        if (specialCharCheck($data['editadres'])) {
            $valuesCorrect = false;
            $inputError['adres'] = true;
        }
        if (specialCharCheck($data['editwoonplaats'])) {
            $valuesCorrect = false;
            $inputError['woonplaats'] = true;
        }
        if (!PostalCodeCheck($data['editpostcode'])) {
            $valuesCorrect = false;
            $inputError['postcode'] = true;
        }
        if (!HuisnummerCheck($data['edithuisnummer'])) {
            $valuesCorrect = false;
            $inputError['huisnummer'] = true;
        }
        if (!empty($data['edithuisnummerT'])) {
            if (!ToevoegingCheck($data['edithuisnummerT'])) {
                $valuesCorrect = false;
                $inputError['huisnummerT'] = true;
            }
        }
        if ($valuesCorrect) {
            print("correct");
        }
        else {
            print("fout");
        }
    }

    mysqli_close($databaseConnection);
} else {
?>
    <script>
        window.location.replace('login.php')
    </script>
<?php
}



?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <table class="editContainer">
        <tr>
            <td>
                <form method="post" action="Logout.php">
                    <input type="submit" value="uitloggen">
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <h1>Accountgegevens</h1>
            </td>
        </tr>
        <form method="post" action="edit.php">
            <tr>
                <td><b class="editUserText">Email</b></td>
                <td><input type="text" class="editText" name="editE-mail" value="<?php echo $name[0]["EmailAddress"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Naam</b><br><label class="smallTextDesc red"><i>Aantal karakters: 3-20, mag geen '@' bevatten</i></label></td>
                <td><input type="text" class="editText" name="editGbrnaam" value="<?php echo $name[0]["logonname"] ?>"></td>
            </tr>
            <tr>
                <td>
                    <h1>Adresgegevens</h1>
                </td>
            </tr>
            <tr>
                <td><b class="editUserText">Woonplaats</b></td>
                <td><input type="text" class="editText" name="editwoonplaats" value="<?php echo $name[0]["residence"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Adres</b></td>
                <td><input type="text" class="editText" name="editadres" value="<?php echo $name[0]["address"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Huisnummer</b></td>
                <td><input type="text" class="editText" name="edithuisnummer" value="<?php echo $name[0]["Housenumber"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Toevoeging</b></td>
                <td><input type="text" class="editText" name="edithuisnummerT" value="<?php echo $name[0]["Addition"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Postcode</b></td>
                <td><input type="text" class="editText" name="editpostcode" value="<?php echo $name[0]["ZIP_code"] ?>"></td>
            </tr>
            <tr>
                <td><input type="submit" value="opslaan" name="accountGegevensAanpassen"></td>
            </tr>
        </form>
        <tr>
            <td>
                <h1>Wachtwoord aanpassen</h1>
            </td>
        </tr>
        <tr>
            <td><b class="editUserText">Huidig wachtwoord</b></td>
            <td><input type="password" class="editText"></td>
        </tr>
        <tr>
            <td><b class="editUserText">Nieuw wachtwoord</b></td>
            <td><input type="password" class="editText"></td>
        </tr>
        <tr>
            <td><b class="editUserText">Nieuw wachtwoord herhalen</b></td>
            <td><input type="password" class="editText"></td>
        </tr>
        <tr>
            <td><input type="submit" value="opslaan"></td>
        </tr>

    </table>
</body>

</html>