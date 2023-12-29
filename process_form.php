<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connessione al database
    $conn = new mysqli("localhost", "root", "", "utenti");

    // Verifica della connessione
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Processa i dati del file CSV
    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == UPLOAD_ERR_OK) {
        $csvFile = $_FILES["csvFile"]["tmp_name"];

        // Apri il file CSV
        $file = fopen($csvFile, "r");

        // Leggi i dati da ogni riga del CSV
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $start_row = 1;
            $nome = $data[1];      // Assumi che la prima colonna sia il nome
            $cognome = $data[2];   // Assumi che la seconda colonna sia il cognome
            $idnumber = $data[3];  // Assumi che la terza colonna sia l'idnumber
            $email = $data[4];     // Assumi che la quarta colonna sia l'email

            // Inserisci i dati nel database (esempio)
            $sql = "INSERT INTO utenti (nome, cognome, idnumber, email) VALUES ('$nome', '$cognome', '$idnumber', '$email')";

            if ($conn->query($sql) !== TRUE) {
                echo "Errore nell'inserimento dei dati: " . $conn->error;
            }
        }

        // Chiudi il file CSV
        fclose($file);
    }

    // Processa gli altri dati del form
    $auth = $_POST["auth"];
    $gruppo = $_POST["gruppo"];
    $group = $_POST["group"];
    $course = $_POST["course"];

    // Esegui l'inserimento nel database (esempio)
    $sql = "INSERT INTO utenti (auth, gruppo, group, course) VALUES ('$auth', '$gruppo', '$group', '$course')";

    if ($conn->query($sql) === TRUE) {
        echo "Dati inseriti con successo";
    } else {
        echo "Errore nell'inserimento dei dati: " . $conn->error;
    }

    // Chiudi la connessione al database
    $conn->close();
}
?>

