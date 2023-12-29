<?php
require 'database.php';

// Ottieni i dati dalla richiesta AJAX
$rowId = $_POST['rowId'];
$selectedValue = $_POST['selectedValue'];

// Esegui l'aggiornamento nella tabella
$updateQuery = "UPDATE utenti SET auth = '$selectedValue' WHERE id = $rowId";
$result = mysqli_query($connessione, $updateQuery);

// Verifica il risultato dell'aggiornamento
if ($result) {
    echo "Aggiornamento riuscito";
} else {
    echo "Errore nell'aggiornamento: " . mysqli_error($connessione);
}

// Chiudi la connessione al database
mysqli_close($connessione);
?>
