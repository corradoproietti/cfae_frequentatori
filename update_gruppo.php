<?php
require 'database.php';

// Ottieni i dati dalla richiesta AJAX
$rowId = $_POST['rowId'];
$selectedValue = $_POST['selectedValue'];

// Componi il valore di memberOf
$memberOfValue = 'CN=' . $selectedValue . ',OU=Gruppi,DC=cenforaven,DC=edu';

// Esegui l'aggiornamento nella tabella
$updateQuery = "UPDATE utenti SET gruppi = '$selectedValue', memberOf = '$memberOfValue' WHERE id = $rowId";
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
