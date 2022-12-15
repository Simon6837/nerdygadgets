<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
?>

<div class="FormBackground">
    <h1>Inloggen</h1><br>

    <form action="login.php" method=post>
        <label class="inputTextFormTitleFirst">Gebruikersnaam/ E-mailadres</label>
        <input class="inputTextForm" type="text" name="Gbrnaam" placeholder="gebruiker@voorbeeld.nl" required />
        <label class="inputTextFormTitle">Wachtwoord</label>
        <input class="inputTextForm" type="password" name="WW" placeholder="wachtwoord" required>
        <input class="button2 accountAanmakenTopMargin" type="submit" name="button" value="Inloggen">
    </form>

    <?php

    // if button is pressed checks if array contains email or username
    if (isset($_POST['button'])) {
        if (str_contains($_POST['Gbrnaam'], '@')) {
            $id = 'emailaddress';
        } else {
            $id = 'logonname';
        }
        // selects the password and personid from the corresponding username or email
        $passworddatabase = mysqli_prepare($databaseConnection, "SELECT logonname, fullname, address, residence, emailaddress, hashedpassword, personid FROM people WHERE $id = ?");
        mysqli_stmt_bind_param($passworddatabase, 's', $_POST['Gbrnaam']);
        mysqli_stmt_execute($passworddatabase);
        // returns the results from the query as a string
        $passresult = mysqli_stmt_get_result($passworddatabase);
        // puts returned strings into an array
        $loginData = mysqli_fetch_all($passresult, MYSQLI_ASSOC);
        mysqli_close($databaseConnection);
        // if the passhash from the database has been set into the array verifies the passhash with the inputted password
        if (isset($loginData[0])) {
            $userDetails = $loginData[0];
            $passcheck = password_verify($_POST['WW'], $userDetails['hashedpassword']);
            // if password is correct saves login info in session and returns user to cart
            if ($passcheck) {
                $_SESSION['loggedInUserId'] = $userDetails['personid'];
                $_SESSION['logonname'] = $userDetails['logonname'];
                $_SESSION['fullname'] = $userDetails['fullname'];
                $_SESSION['address'] = $userDetails['address'];
                $_SESSION['residence'] = $userDetails['residence'];
                $_SESSION['emailaddress'] = $userDetails['emailaddress'];

                $script = "<script>window.location = './viewOrder.php';</script>";
                echo $script;
            } else {
                print("<label class='inputError'><i>De ingevoerde combinatie van gebruikersnaam en wachtwoord bestaat niet</i></label><br>");
            }
        } else {
            print("<label class='inputError'><i>De ingevoerde combinatie van gebruikersnaam en wachtwoord bestaat niet</i></label><br>");
        }
    }

    ?>
</div>