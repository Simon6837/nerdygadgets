<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
?>

<h1>Inloggen</h1><br><br>

<form action="login.php" method = post>
    <label>Gebruikersnaam/ E-mailadres</label>
    <input type="text" name="Gbrnaam" placeholder="gebruiker@voorbeeld.nl" required/>
    <label>Wachtwoord</label>
    <input type="password" name="WW" placeholder="wachtwoord" required>
    <input class="button2" type="submit" name="button" value="Inloggen">
</form>

<?php

// if button is pressed checks if array contains email or username
if (isset($_POST['button'])){
    if(str_contains($_POST['Gbrnaam'], '@')){
        $id = 'emailaddress';
    } else{
        $id = 'logonname';
    }
    // selects the password and personid from the corresponding username or email
    $passworddatabase = mysqli_prepare($databaseConnection, "SELECT hashedpassword, personid FROM people WHERE $id = ?");
    mysqli_stmt_bind_param($passworddatabase, 's', $_POST['Gbrnaam']);
    mysqli_stmt_execute($passworddatabase);
    // returns the results from the query as a string
    $passresult = mysqli_stmt_get_result($passworddatabase);
    // puts returned strings into an array
    $password = mysqli_fetch_all($passresult, MYSQLI_ASSOC);
    mysqli_close($databaseConnection);
    // if the passhash from the database has been set into the array verifies the passhash with the inputted password
    if (isset($password[0])) {
        $userDetails = $password[0];
        $passcheck = password_verify($_POST['WW'], $userDetails['hashedpassword']);
        // if password is correct saves login info in session and returns user to cart
        if ($passcheck){
            $_SESSION['loggedInUserId'] = $userDetails['personid'];

        $script = "<script>window.location = 'http://localhost/nerdygadgets/';</script>";
        echo $script;

        } else{
            print('de ingevoerde combinatie van gebruikersnaam en wachtwoord bestaat niet');
        }
    } else{
        print('de ingevoerde combinatie van gebruikersnaam en wachtwoord bestaat niet');
    }



}

?>

<!--mysqli_query($databaseConnection, "SELECT logonname, hashedpassword FROM people WHERE logonname = $_POST['Gbrnaam']")-->