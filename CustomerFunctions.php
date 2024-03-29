<?php
include_once __DIR__ . "/header.php";
include "DatabaseFunctions.php";

/**
 * @param $customerData -- array with all the customer data
 * add a customer to the database
 */ 
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
    //set the userdata in the session and log the user in
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
/**
 * @param $data -- array with all the customer data
 * edit a customer in the database
 */
function klantGegevensBewerken($data)
{
    $databaseConnection = connectToDatabase();
    $editCustomer = mysqli_prepare($databaseConnection, "UPDATE people SET logonName = ?, FullName  = ?, EmailAddress = ?, residence = ?, address = ?, Housenumber = ?, Addition = ?, ZIP_code = ? WHERE personid = " . $_SESSION['userdata']['loggedInUserId'] . "");
    mysqli_stmt_bind_param($editCustomer, 'sssssiss', $data['editUserName'], $data["editFullname"], $data["editE-mail"], $data["editwoonplaats"], $data["editadres"], $data["edithuisnummer"], $data["edithuisnummerT"], $data["editpostcode"]);
    $result = mysqli_stmt_execute($editCustomer);
    mysqli_close($databaseConnection);
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
/**
 * @param $email
 * checks if email is valid
 * @return bool
 */
function emailCheck($email)
{
    return preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/', $email);
}
/**
 * @param $haystack
 * checks if password is valid with the following rules
 * 1. must contain at least 1 uppercase letter
 * 2. must contain at least 1 lowercase letter
 * 3. must contain at least 1 number
 * 4. must contain at least 1 special character
 * 5. must be at least 12 characters long
 * @return bool
 */
function passwordCheck($haystack)
{
    $uppercase = preg_match('@[A-Z]@', $haystack);
    $lowercase = preg_match('@[a-z]@', $haystack);
    $number    = preg_match('@[0-9]@', $haystack);
    $specialChars = preg_match('@[^\w]@', $haystack);
    return $uppercase && $lowercase && $number && $specialChars && strlen($haystack >= 12);
};

/**
 * @param $haystack
 * checks if username is valid with the following rules
 * 1. must be between 3 and 20 characters long
 * 2. must not contain @
 * @return bool
 */
function usernameCheck($haystack)
{
    return (2 < strlen($haystack) && strlen($haystack) < 21) && !str_contains($haystack, '@');
};
/**
 * @param $haystack
 * checks if name is valid with the following rules
 * 1. firrst 4 characters must be letters
 * 2. last 2 characters must be a number
 * @return bool
 */
function PostalCodeCheck($haystack)
{
    return is_numeric(substr($haystack, 0, -2)) && ctype_alpha(substr($haystack, 4));
}
/**
 * @param $haystack
 * checks if name is valid with the following rules
 * 1. must be a number
 * @return bool
 */
function HuisnummerCheck($haystack)
{
    return is_numeric($haystack);
}

/**
 * @param $haystack
 * check if addition is valid with the following rules
 * 1. must be a letter
 * @return bool
 */
function ToevoegingCheck($haystack)
{
    return ctype_alpha($haystack);
}

/**
* Get the hashed password from the database
* @return string - The hashed password
*/
function getHashedPaswordFromDatabase()
{
    $databaseConnection = connectToDatabase();
    $query = mysqli_prepare($databaseConnection, "SELECT HashedPassword FROM people WHERE personid = " . $_SESSION['userdata']['loggedInUserId'] . "");
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($databaseConnection);
    return $data[0]['HashedPassword'];
}

/**
* Update the password in the database
* @param $newPassword - The new password
*/
function updatePassword($newPassword)
{
    $databaseConnection = connectToDatabase();
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $query = mysqli_prepare($databaseConnection, "UPDATE people SET HashedPassword = ? WHERE personid = " . $_SESSION['userdata']['loggedInUserId'] . "");
    mysqli_stmt_bind_param($query, "s", $hashedPassword);
    mysqli_stmt_execute($query);
    mysqli_close($databaseConnection);
}
/**
* Checks if the old password is correct, if the new passwords match and if the new password meets the requirements
* If all checks pass, the password is updated in the database
* Returns an array with status and type
* status can be 'success' or 'error'
* type can be 'oldPassword', 'match' or 'requirments'
*/
function changePassword($oldPassword, $newPassword, $newPasswordRepeat)
{
    if (!password_verify($oldPassword, getHashedPaswordFromDatabase())) {
        return ['status' => 'error', 'type' => 'oldPassword'];
    }

    if ($newPassword !== $newPasswordRepeat) {
        return ['status' => 'error', 'type' => 'match'];
    }

    if (!passwordCheck($newPassword)) {
        return ['status' => 'error', 'type' => 'requirments'];
    }
    updatePassword($newPassword);
    return ['status' => 'success'];
}
