<?php
include_once __DIR__ . "/header.php";
include "DatabaseFunctions.php";

// adds inputted values into the people table in the database
function klantGegevensToevoegen($customerData)
{
    $databaseConnection = connectToDatabase();
    $dateT = date('Y-m-d H:i:s');
    $insertCustomer = mysqli_prepare($databaseConnection, "INSERT INTO people (FullName, PreferredName,
SearchName, address, residence, IsPermittedToLogon, LogonName, IsExternalLogonProvider, HashedPassword, IsSystemUser,
IsEmployee, IsSalesperson, EmailAddress, LastEditedBy, ValidFrom, ValidTo, Housenumber, ZIP_code, addition) VALUES (?, ?, ?, ?, ?, 1, ?, 0, ?, 1, 0, 0, ?, 1, ?, ?, ?, ?,?)");

    mysqli_stmt_bind_param(
        $insertCustomer,
        'ssssssssssiss',
        $customerData['naam'],
        $customerData['naam'],
        $customerData['naam'],
        $customerData['adres'],
        $customerData['woonplaats'],
        $customerData['Gbrnaam'],
        $customerData['hWW'],
        $customerData['E-mail'],
        $dateT,
        $dateT,
        $customerData['huisnummer'],
        $customerData['postcode'],
        $customerData['huisnummerT']
    );
    $result = mysqli_stmt_execute($insertCustomer);
    $last_id = mysqli_insert_id($databaseConnection);
    mysqli_close($databaseConnection);
    $_SESSION['userdata']['loggedInUserId'] = $last_id;
    $_SESSION['userdata']['logonname'] = $customerData['Gbrnaam'];
    $_SESSION['userdata']['fullname'] = $customerData['naam'];
    $_SESSION['userdata']['address'] = $customerData['adres'];
    $_SESSION['userdata']['residence'] = $customerData['woonplaats'];
    $_SESSION['userdata']['emailaddress'] = $customerData['E-mail'];
    $_SESSION['userdata']['housenumber'] = $customerData['huisnummer'];
    $_SESSION['userdata']['ZIP_code'] = $customerData['postcode'];
    $_SESSION['userdata']['addition'] = $customerData['huisnummerT'];
    return $result;
}

// defines all special characters we don't want in name, address and residence and checks if inputted values contains said values
function specialCharCheck($haystack)
{
    $chars = array(
        '!', '?', ':', ';', '"', '#', '@', '$', '%', '^', '*', '(', ')', '=', '+', '{', '}', '|', '>', '<', '~', '`'
    );

    foreach ($chars as $char) {
        if (str_contains($haystack, $char)) {
            return TRUE;
        }
    }
    return false;
}

function emailCheck($email) {
    return preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $email);
}

function passwordCheck($haystack)
{
    $uppercase = preg_match('@[A-Z]@', $haystack);
    $lowercase = preg_match('@[a-z]@', $haystack);
    $number    = preg_match('@[0-9]@', $haystack);
    $specialChars = preg_match('@[^\w]@', $haystack);
    $check = $uppercase && $lowercase && $number && $specialChars && strlen($haystack >= 12);
    return $check;
};

function usernameCheck($haystack)
{
    $check = (2 < strlen($haystack) && strlen($haystack) < 21) && !str_contains($haystack, '@');
    return $check;
};

function PostalCodeCheck($haystack)
{
    $check1 = is_numeric(substr($haystack, 0, -2));
    $check2 = ctype_alpha(substr($haystack, 4));
    if ($check1 && $check2) {
        return true;
    } else {
        return false;
    }
}

function HuisnummerCheck($haystack)
{
    $check = is_numeric($haystack);
    return $check;
}

function ToevoegingCheck($haystack)
{
    return ctype_alpha($haystack);
}
