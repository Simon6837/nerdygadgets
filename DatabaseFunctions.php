<?php
/**
 * connect to the database
 * with the given credentials
 */
function maakVerbinding() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $databasename = "Klantenservice";
    $connection = mysqli_connect($host, $user, $pass, $databasename);
    return $connection;
}
/**
 * select all customers from the database
 */
function selecteerKlanten($connection) {
    $sql = "SELECT nummer, naam, woonplaats FROM klant ORDER BY naam";
    $result = mysqli_fetch_all(mysqli_query($connection, $sql),MYSQLI_ASSOC);
    return $result;
}
/**
 * close the connection to the database
 */
function sluitVerbinding($connection) {
    mysqli_close($connection);
}