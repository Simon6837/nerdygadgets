<?php
include __DIR__ . "/header.php";
$databaseConnection = connectToDatabase();
//HAAL DIT WEG ALS DE LOGIN KLAAR IS
$_SESSION['loggedInUserId'] = 6;
if (isset($_SESSION['loggedInUserId'])) {
    $nameDatabase = mysqli_prepare($databaseConnection, "SELECT fullname, logonname, preferredname FROM people WHERE personid = " . $_SESSION['loggedInUserId'] . "");
    mysqli_stmt_execute($nameDatabase);
    $nameResult = mysqli_stmt_get_result($nameDatabase);
    $name = mysqli_fetch_all($nameResult, MYSQLI_ASSOC);
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
                <h1>Accountgegevens</h1>
            </td>
        </tr>
        <tr>
            <td><a class="editUserText">Email</a></td>
            <td><input type="text" disabled class="editText" value="<?php echo $name[0]["logonname"] ?>"></td>
        </tr>
        <tr>
            <td><a class="editUserText">Wachtwoord</a></td>
            <td><input type="password" disabled class="editText" value="wachtwoord"></td>
            <td><button onclick="console.log('hi')" class="editButton editButtonBackground"><i class="editButton fas fa-wrench red"></i></button></td>
        </tr>
        <tr>
            <td><a class="editUserText">Naam</a></td>
            <td><input type="text" disabled class="editText" value="<?php echo $name[0]["fullname"] ?>"></td>
            </div>
        </tr>
        <tr>
            <h1>Adresgegevens</h1>
        </tr>
    </table>
</body>

</html>