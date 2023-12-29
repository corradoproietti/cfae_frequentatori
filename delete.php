<?php
require 'database.php';

// Verifica se è stata inviata una richiesta di eliminazione
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Esegui la query per eliminare l'utente dal database
    $deleteQuery = "DELETE FROM utenti WHERE id = $userId";
    if (mysqli_query($connessione, $deleteQuery)) {
        echo '<script>alert("Utente eliminato con successo!");</script>';
    } else {
        echo '<script>alert("Errore durante l\'eliminazione dell\'utente.");</script>';
    }
}

// Reindirizza alla pagina esportCSV.php dopo l'eliminazione
header("Location: esportaCSV.php");
exit();

?>
