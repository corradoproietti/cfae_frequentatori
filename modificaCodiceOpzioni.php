<?php
require 'database.php';

// Verifica se è stato ricevuto un ID tramite GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query per ottenere i dati dalla tabella utenti
    $query = "SELECT nome, cognome, userAccountControl FROM utenti WHERE id = ?";

    // Preparazione della query
    $stmt = $connessione->prepare($query);
    $stmt->bind_param("i", $id); // 'i' indica che si tratta di un intero

    // Esecuzione della query
    $stmt->execute();

    // Associazione dei risultati della query alle variabili
    $stmt->bind_result($nome, $cognome, $userAccountControl);

    // Recupero dei risultati
    $stmt->fetch();

    // Chiusura dello statement
    $stmt->close();

}

$userAccountControlArray = isset($_POST['userAccountControl']) ? $_POST['userAccountControl'] : array();
    
// Inizializza una variabile per memorizzare la somma dei valori di codice
$totalCodiceSum = 0;

foreach ($userAccountControlArray as $selectedUserAccountControl) {
    // Ottieni il valore di codice per il controllo dell'account utente selezionato
    $codiceQuery = "SELECT codice FROM userAccountControl WHERE codice = ?";
    $codiceStmt = $connessione->prepare($codiceQuery);
    $codiceStmt->bind_param("i", $selectedUserAccountControl);
    $codiceStmt->execute();
    $codiceStmt->bind_result($codice);

    // Recupera e accumula la somma dei valori di codice
    while ($codiceStmt->fetch()) {
        $totalCodiceSum += $codice;
    }

    // Chiudi la dichiarazione di codice
    $codiceStmt->close();
}

// Assegna la somma totale dei valori di codice al campo userAccountControl
$userAccountControl = $totalCodiceSum;

if (isset($_POST['import'])) {
    // Esegui l'aggiornamento del campo userAccountControl nella tabella utenti
    $updateQuery = "UPDATE utenti SET userAccountControl = ? WHERE id = ?";
    $updateStmt = $connessione->prepare($updateQuery);
    $updateStmt->bind_param("ii", $userAccountControl, $id);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Aggiorna eventualmente altre colonne della tabella utenti se necessario

    // Redirect o visualizza un messaggio di successo
    header("Location: esportaCSV.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="it">

<head>
    <title>Portale Inserimento Utenti AD & MOODLE</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>

<div class="container-fluid bg-success text-white text-center">
        <h1><a href="index.php" style="color: inherit; text-decoration: none;">
        <i class="fa-solid fa fa-users"></i> Portale Inserimento Utenti AD & MOODLE</a></h1>
    </div>

<p></p>
    
<form method="post" enctype="multipart/form-data">
    <div class="container border border-success p-3">
    <div class="text-center">
        <h3>Vuoi Modificare le Opzioni Utente di <b><?php echo $nome; ?> <?php echo $cognome; ?></b>?</h3>
    </div>
        <?php
        // Populate options dynamically from the 'userAccountControl' table
        $userAccountControlQuery = "SELECT * FROM userAccountControl";
        $userAccountControlResult = $connessione->query($userAccountControlQuery);

        while ($row = $userAccountControlResult->fetch_assoc()) {
            echo '<div class="form-check">
                    <input type="checkbox" class="form-check-input" name="userAccountControl[]" value="' . $row['codice'] . '" id="userAccountControl' . $row['codice'] . '">
                    <label class="form-check-label" for="userAccountControl' . $row['codice'] . '">' . $row['nome'] . '</label>
                  </div>';
        }
        ?>

<p></p>

        <div class="text-center">
            <button type="submit" class="btn btn-success" name="import">Modifica</button>
        </div>
    </div>
</form>

<style>
  footer {
  background-color: #198754;
  color: #fff;
  text-align: center;
  padding: 0px;
  position: fixed;
  bottom: 0;
  width: 100%;
}
</style>

    <footer>
        <i>Per info e suggerimenti per migliorare il portale: luca.dascanio@aeronautica.difesa.it</a>
    </footer>

</body>
</html>