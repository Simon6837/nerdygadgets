<?php
include_once __DIR__ . "/header.php";
include "DatabaseFunctions.php";

// adds inputted values into the people table in the database
function klantGegevensToevoegen($customerData){
    $databaseConnection = connectToDatabase();
    $dateT = date('Y-m-d H:i:s');
    $insertCustomer = mysqli_prepare($databaseConnection, "INSERT INTO people (FullName, PreferredName,
SearchName, address, residence, IsPermittedToLogon, LogonName, IsExternalLogonProvider, HashedPassword, IsSystemUser,
IsEmployee, IsSalesperson, EmailAddress, LastEditedBy, ValidFrom, ValidTo, Housenumber, ZIP_code, addition) VALUES (?, ?, ?, ?, ?, 1, ?, 0, ?, 1, 0, 0, ?, 1, ?, ?, ?, ?,?)");

    mysqli_stmt_bind_param($insertCustomer, 'ssssssssssiss', $customerData['naam'], $customerData['naam'],
        $customerData['naam'], $customerData['adres'], $customerData['woonplaats'], $customerData['Gbrnaam'],
        $customerData['hWW'], $customerData['E-mail'], $dateT, $dateT, $customerData['huisnummer'], $customerData['postcode'], $customerData['huisnummerT']);
    $result = mysqli_stmt_execute($insertCustomer);
    mysqli_close($databaseConnection);
    return $result;
}

function klantGegevensBewerken($data){
    $databaseConnection = connectToDatabase();
    // $dateT = date('Ý-m-d H:i:s');
    $editCustomer = mysqli_prepare($databaseConnection, "UPDATE people SET  logonname = ?, EmailAddress = ?, residence = ?, address = ?, Housenumber = ?, Addition = ?, ZIP_code = ? WHERE personid = " . $_SESSION['userdata']['loggedInUserId'] . "");
    mysqli_stmt_bind_param($editCustomer, 'ssssiss', $data["editGbrnaam"], $data["editE-mail"], $data["editwoonplaats"], $data["editadres"], $data["edithuisnummer"],$data["edithuisnummerT"], $data["editpostcode"]);
    $result = mysqli_stmt_execute($editCustomer);
    mysqli_close($databaseConnection);
    return $result;
}

// defines all special characters we don't want in name, address and residence and checks if inputted values contains said values
function specialCharCheck ($haystack) {
    $chars = array(
        '!','?',':',';','"','#','@','$','%','^','*','(',')','=','+','{','}','|','>','<','~','`');

    foreach ($chars as $char) {
        if (str_contains($haystack , $char)) {
            return TRUE;
        }
    }
    return false;
}

function emailCheck ($haystack) {
    if (str_contains($haystack, '@') and strlen($haystack >= 6 and str_contains((substr($haystack, -5)), '.'))){
        return true;
    }
    else {
        return false;
    }
};

function passwordCheck ($haystack) {
    $uppercase = preg_match('@[A-Z]@', $haystack);
    $lowercase = preg_match('@[a-z]@', $haystack);
    $number    = preg_match('@[0-9]@', $haystack);
    $specialChars = preg_match('@[^\w]@', $haystack);
    $check = $uppercase && $lowercase && $number && $specialChars && strlen($haystack >= 12);
    return $check;
};

function usernameCheck ($haystack) {
    $check = (2 < strlen($haystack) && strlen($haystack) <21) && !str_contains($haystack, '@');
    return $check;
};

function PostalCodeCheck ($haystack){
    $check1 = is_numeric(substr($haystack, 0, -2));
    $check2 = ctype_alpha(substr($haystack, 4));
    if ($check1 && $check2){
        return true;
    }
    else{
        return false;
    }
}

function HuisnummerCheck ($haystack){
    $check = is_numeric($haystack);
    return $check;
}

function ToevoegingCheck ($haystack){
    return ctype_alpha($haystack);
}