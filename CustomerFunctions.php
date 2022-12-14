<?php
include __DIR__ . "/header.php";
include "DatabaseFunctions.php";

// adds inputted values into the people table in the database
function klantGegevensToevoegen($customerData){
    $databaseConnection = connectToDatabase();
    $dateT = date('Y-m-d H:i:s');
    $insertCustomer = mysqli_prepare($databaseConnection, "INSERT INTO people (FullName, PreferredName,
SearchName, address, residence, IsPermittedToLogon, LogonName, IsExternalLogonProvider, HashedPassword, IsSystemUser,
IsEmployee, IsSalesperson, EmailAddress, LastEditedBy, ValidFrom, ValidTo) VALUES (?, ?, ?, ?, ?, 1, ?, 0, ?, 1, 0, 0, ?, 1, ?, ?)");

    mysqli_stmt_bind_param($insertCustomer, 'ssssssssss', $customerData['naam'], $customerData['naam'],
        $customerData['naam'], $customerData['adres'], $customerData['woonplaats'], $customerData['Gbrnaam'],
        $customerData['hWW'], $customerData['E-mail'], $dateT, $dateT);
    $result = mysqli_stmt_execute($insertCustomer);
    mysqli_close($databaseConnection);
    return $result;
}

// defines all special characters we dont want in name, address and residence and checks if inputted values contains said values
function specialCharCheck ($haystack) {
    $chars = array(
        '!',
        '?',
        ':',
        ';',
        '"',
        '#',
        '@',
        '$',
        '%',
        '^',
        '*',
        '(',
        ')',
        '=',
        '+',
        '{',
        '}',
        '|',
        '>',
        '<',
        '~',
        '`'
    );

    foreach ($chars as $char) {
        if (str_contains($haystack , $char)) {
            return TRUE;
        }
    }
    Return False;
}

function emailCheck ($haystack) {
    $check = str_contains($haystack, '@') and strlen($haystack >= 6 and str_contains((substr($haystack, -5)), '.'));
    return $check;
};

function wachtwoordCheck ($haystack) {
    $uppercase = preg_match('@[A-Z]@', $haystack);
    $lowercase = preg_match('@[a-z]@', $haystack);
    $number    = preg_match('@[0-9]@', $haystack);
    $specialChars = preg_match('@[^\w]@', $haystack);
    $check = $uppercase && $lowercase && $number && $specialChars && strlen($haystack >= 12);
    return $check;
};

function gebruikersnaamCheck ($haystack) {
    $check = (2 < strlen($haystack) && strlen($haystack) <21) && str_contains($haystack, '@');
    return $check;
};