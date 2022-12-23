<?php
include_once __DIR__ . "/header.php";
include_once 'CustomerFunctions.php';
$databaseConnection = connectToDatabase();
if (isset($_SESSION['userdata']['loggedInUserId'])) {

    $data["editE-mail"] = $_POST["editE-mail"] ?? "";
    $data["editGbrnaam"] = $_POST["editGbrnaam"] ?? "";
    $data["editadres"] = $_POST["editadres"] ?? "";
    $data["editwoonplaats"] = $_POST["editwoonplaats"] ?? "";
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
            klantGegevensBewerken($data);
            //hier komt een redirect als fix
        }
    }
    $nameDatabase = mysqli_prepare($databaseConnection, "SELECT logonname, EmailAddress, residence, address, Housenumber, Addition, ZIP_code FROM people WHERE personid = " . $_SESSION['userdata']['loggedInUserId'] . "");
    mysqli_stmt_execute($nameDatabase);
    $nameResult = mysqli_stmt_get_result($nameDatabase);
    $name = mysqli_fetch_all($nameResult, MYSQLI_ASSOC);
    mysqli_close($databaseConnection);
    //Checks if the password form is submitted
    if (isset($_POST['changePassword'])) {
        $response = changePassword($_POST['oldPassword'], $_POST['newPassword'], $_POST['newPasswordRepeat']);
        if ($response['status'] == 'error') {
            if ($response['type'] == 'oldPassword') {
                $inputError['oldPassword'] = true;
            }
            if ($response['type'] == 'match') {
                $inputError['matchPassword'] = true;
            }
            if ($response['type'] == 'requirments') {
                $inputError['requirementsPassword'] = true;
            }
        }
        if ($response['status'] == 'success') {
            $inputError['successPassword'] = true;
        }
    }
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
        <form method="post" action="editCustomer.php">
            <tr>
                <td><b class="editUserText">Email</b>
                    <br>
                    <?php if (isset($inputError['email'])) {
                        print("<label class='inputError'><i>Ongeldig e-mailadres</i></label><br>");
                    } ?>
                </td>
                <td><input type="text" class="editText" name="editE-mail" value="<?php echo $name[0]["EmailAddress"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Naam</b>
                    <br>
                    <?php if (isset($inputError['gebruikersnaam'])) {
                        print("<label class='inputError'><i>Gebruikernaam voldoet niet aan de eisen</i></label><br>");
                    } ?>
                </td>
                <td><input type="text" class="editText" name="editGbrnaam" value="<?php echo $name[0]["logonname"] ?>"></td>
            </tr>
            <tr>
                <td>
                    <h1>Adresgegevens</h1>
                </td>
            </tr>
            <tr>
                <td><b class="editUserText">Adres</b>
                    <br>
                    <?php if (isset($inputError['adres'])) {
                        print("<label class='inputError'><i>Adres voldoet niet aan de eisen</i></label><br>");
                    } ?>
                </td>
                <td><input type="text" class="editText" name="editadres" value="<?php echo $name[0]["address"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Huisnummer</b></td>
                <br>
                <?php if (isset($inputError['huisnummer'])) {
                    print("<label class='inputError'><i>Huisnummer voldoet niet aan de eisen</i></label><br>");
                } ?>
                <td><input type="text" class="editText" name="edithuisnummer" value="<?php echo $name[0]["Housenumber"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Toevoeging</b></td>
                <br>
                <?php if (isset($inputError['huisnummerT'])) {
                    print("<label class='inputError'><i>Huisnummer toevoeging voldoet niet aan de eisen</i></label><br>");
                } ?>
                <td><input type="text" class="editText" name="edithuisnummerT" value="<?php echo $name[0]["Addition"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Postcode</b></td>
                <br>
                <?php if (isset($inputError['postcode'])) {
                    print("<label class='inputError'><i>Postcode voldoet niet aan de eisen</i></label><br>");
                } ?>
                <td><input type="text" class="editText" name="editpostcode" value="<?php echo $name[0]["ZIP_code"] ?>"></td>
            </tr>
            <tr>
                <td><b class="editUserText">Woonplaats</b>
                    <br>
                    <?php if (isset($inputError['woonplaats'])) {
                        print("<label class='inputError'><i>Woonplaats voldoet niet aan de eisen</i></label><br>");
                    } ?>
                </td>
                <td><input type="text" class="editText" name="editwoonplaats" value="<?php echo $name[0]["residence"] ?>"></td>
            </tr>
            <tr>
                <td><input type="submit" value="opslaan" name="accountGegevensAanpassen"></td>
            </tr>
        </form>
        <tr>
            <td>
                <h1>Wachtwoord aanpassen</h1>
                <br>
                <?php if (isset($inputError['successPassword'])) {
                    print("<label style='color: green'>Het wachtwoord is aangepast</label><br>");
                } ?>
            </td>
        </tr>
        <form method="post" action="editCustomer.php">
            <tr>
                <td>
                    <b class="editUserText">Huidig wachtwoord</b>
                    <br>
                    <?php if (isset($inputError['oldPassword'])) {
                        print("<label class='inputError'><i>Het oude wachtwoord is fout</i></label><br>");
                    } ?>
                </td>
                <td><input type="password" class="editText" name="oldPassword"></td>
            </tr>
            <tr>
                <td>
                    <b class="editUserText">Nieuw wachtwoord</b>
                    <br>
                    <?php if (isset($inputError['requirementsPassword'])) {
                        print("<label class='inputError'><i>Het nieuwe wachtwoord voldoet niet aan de eisen</i></label><br>");
                    } ?>
                </td>
                <td><input type="password" class="editText" name="newPassword"></td>
            </tr>
            <tr>
                <td>
                    <b class="editUserText">Nieuw wachtwoord herhalen</b>
                    <br>
                    <?php if (isset($inputError['matchPassword'])) {
                        print("<label class='inputError'><i>het nieuwe wachtwoord komt niet overeen</i></label><br>");
                    } ?>
                </td>
                <td><input type="password" class="editText" name="newPasswordRepeat"></td>
            </tr>
            <tr>
                <td><input type="submit" value="opslaan" name="changePassword"></td>
            </tr>
        </form>
    </table>
</body>

</html>