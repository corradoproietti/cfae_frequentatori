<?php
require 'database.php';

// Eliminazione di tutti i dati quando viene premuto il pulsante "Elimina Tutti i Dati"
if (isset($_POST["delete_all"])) {
    // Connessione al database
    $connessione->autocommit(FALSE); // Disabilita l'autocommit per avviare una transazione

    // Elimina tutte le righe dalla tabella
    $deleteStmt = $connessione->prepare("TRUNCATE TABLE frequentatori");
    if (!$deleteStmt) {
        die("Errore nella preparazione dello statement di eliminazione: " . $connessione->error);
    }

    $deleteStmt->execute();
    if ($deleteStmt->errno) {
        die("Errore durante l'esecuzione dello statement di eliminazione: " . $deleteStmt->error);
    }

    $deleteStmt->close();

    // Conferma la transazione
    $connessione->commit();
    $connessione->autocommit(TRUE); // Riabilita l'autocommit
}

if (isset($_POST["import"])) {
    $file = $_FILES["csv_file"];
    $file_name = $file["name"];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $new_file_name = date("Y-m-d") . " - " . date("h.i.sa") . "." . $file_ext;
    $target_dir = "uploads/" . $new_file_name;

    move_uploaded_file($file["tmp_name"], $target_dir);

    require 'excelReader/excel_reader2.php';
    require 'excelReader/SpreadsheetReader.php';

    // Connessione al database
    $connessione->autocommit(FALSE); // Disable autocommit to start a transaction

    // Delete all rows from the table
    $deleteStmt = $connessione->prepare("TRUNCATE TABLE frequentatori");
    $deleteStmt->execute();
    $deleteStmt->close();

    $reader = new SpreadsheetReader($target_dir);
    $start_row = 1;
    foreach ($reader as $key => $row) {
        if ($key < $start_row) continue;
        $cognome = $row[1];
        $nome = $row[2];
        $email = $row[3];

        // Inserisci nuovi dati, inclusi quelli dal form
        $auth = $_POST['auth']; // Assicurati che il campo auth esista nel tuo form
        $gruppi = isset($_POST['gruppi']) ? implode(',', $_POST['gruppi']) : ''; // Assicurati che il campo gruppi esista nel tuo form
        $group = $_POST['group']; // Assicurati che il campo group esista nel tuo form
        $course = $_POST['course']; // Assicurati che il campo course esista nel tuo form

        $insertStmt = $connessione->prepare("INSERT INTO utenti (cognome, nome, email, auth, gruppi, group, course) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssss", $cognome, $nome, $email, $auth, $gruppi, $group, $course);
        $insertStmt->execute();
        $insertStmt->close();
    }

    // Commit the transaction
    $connessione->commit();
    $connessione->autocommit(TRUE); // Re-enable autocommit
}
?>