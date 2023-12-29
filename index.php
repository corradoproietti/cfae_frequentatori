<?php
 
require 'database.php';
require 'excelReader/excel_reader2.php';
require 'excelReader/SpreadsheetReader.php';

if (isset($_POST["import"])) {

    $file = $_FILES["csv_file"];

    // Validate file type
    $allowed_extensions = array("csv");
    $file_ext = pathinfo($file["name"], PATHINFO_EXTENSION);

    if (!in_array($file_ext, $allowed_extensions)) {
        die("Invalid file type. Please upload a CSV file.");
    }

    // Check if the connection is successful
    if ($connessione->connect_error) {
        die("Connection failed: " . $connessione->connect_error);
    }

    // Clear existing data in the 'utenti' table
    $clearStmt = $connessione->prepare("TRUNCATE TABLE utenti");

    if (!$clearStmt) {
        die("Prepare failed: " . $connessione->error);
    }

    $clearStmt->execute();

    if ($clearStmt->error) {
        die("Execution failed: " . $clearStmt->error);
    }

    $clearStmt->close();

    $file_name = $file["name"];
    $new_file_name = date("Y-m-d") . " - " . date("h.i.sa") . "." . $file_ext;
    $target_dir = "uploads/" . $new_file_name;

    move_uploaded_file($file["tmp_name"], $target_dir);

    // Inizio della lettura del file CSV
    $reader = new SpreadsheetReader($target_dir);
    $start_row = 1;

    // Prepare the statement outside the loop
    $insertStmt = $connessione->prepare("INSERT INTO utenti (nome, cognome, idnumber, email, auth, gruppi, `group`, `course`, `userAccountControl`, memberOf) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$insertStmt) {
        die("Prepare failed: " . $connessione->error);
    }

    $insertStmt->bind_param("ssssssssss", $nome, $cognome, $idnumber, $email, $auth, $gruppi, $group, $course, $userAccountControl, $memberOf);

    foreach ($reader as $key => $row) {
        if ($key < $start_row) continue;
    
        // Assicurati che gli indici corrispondano alle colonne desiderate
        $nome = ucwords(strtolower($row[1])); // Capitalize the first letter of each word
        $cognome = strtoupper($row[2]); // Converti in maiuscolo
        $idnumber = $row[3];
        $email = strtolower($row[4]); // Converti l'email in minuscolo

        // Controllo email valida
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailNonValidi[] = array('nome' => $nome, 'cognome' => $cognome, 'email' => $email);
            continue; // Passa alla prossima iterazione
        }
          
    
    
        // Nuovi input: auth, gruppi, group, course, and userAccountControl
        $auth = $_POST['auth'];
        $gruppi = $_POST['gruppi'];
        $course = strtoupper($_POST['course']); // Tutto Maiuscolo
        $group = strtoupper($_POST['group']); // Tutto Maiuscolo
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
    
        // Set the value of $memberOf based on the selected radio button
        $memberOf = $_POST['memberOf'];
    
        // Esegui la dichiarazione preparata una sola volta al di fuori del ciclo
        $insertStmt->execute();
    
        if ($insertStmt->error) {
            die("Esecuzione fallita: " . $insertStmt->error);
        }
    }
    


    // Dopo il ciclo, verifica se ci sono email non valide
    if (!empty($emailNonValidi)) {
        // Salva i dati nel database, indipendentemente dalla validità dell'email
        foreach ($emailNonValidi as $entry) {
            $nome = $entry['nome'];
            $cognome = $entry['cognome'];
            $idnumber = $entry['idnumber'];
            $email = $entry['email'];

            // Utilizza una query senza dichiarazione preparata per gestire l'email
            $query = "INSERT INTO utenti (nome, cognome, idnumber, email, auth, gruppi, `group`, `course`, `userAccountControl`, memberOf) VALUES ('$nome', '$cognome', '$idnumber', '$email', '$auth', '$gruppi', '$group', '$course', '$userAccountControl', '$memberOf')";
            
            $result = $connessione->query($query);

            if (!$result) {
                die("Esecuzione fallita: " . $connessione->error);
            }
        }

        // Reindirizza a correggiMail.php
        header("Location: correggiMail.php");
        exit;
    }

    // Visualizza l'allerta di successo utilizzando JavaScript
    // echo '<script>alert("Dati importati con successo!");</script>';

    // Reindirizza a esportaCSV.php dopo aver visualizzato l'allerta
    echo '<script>window.location.href = "esportaCSV.php";</script>';
    exit;

    // Chiudi la dichiarazione preparata e la connessione al database al di fuori del ciclo
    $insertStmt->close();
    $connessione->close();
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
<h1><a href="index.php" style="color: inherit; text-decoration: none;"><i class="fa-solid fa fa-users"></i> Portale Inserimento Utenti AD & MOODLE</a></h1>
</div>
    <div class="container mt-3">

    <form method="post" enctype="multipart/form-data">
        <div class="form-group border border-success p-1">
        <div style="display: flex; align-items: center; justify-content: space-between;">
    <label for="csv_file" class="form-label"><b>Carica File CSV:</b></label>
    <div>
        <a href="template CSV.csv" style="color: red; text-decoration: none; font-size: smaller;">
        <i class="fa fa-file" aria-hidden="true"></i> Clicca qui per scaricare esempio di modello CSV
        </a>
    </div>
</div>
            <input class="form-control" type="file" id="formFile" name="csv_file" id="csv_file" accept=".csv" required>
        </div>

<p></p>

        <div class="form-group border border-success p-1">
            <label for="auth"><b>Seleziona AUTH:</b></label>
            <?php
            // Populate options dynamically from the 'gruppi' table
            $authQuery = "SELECT * FROM auth";
            $authResult = $connessione->query($authQuery);

            while ($row = $authResult->fetch_assoc()) {
                echo '<div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" name="auth" value="' . $row['auth'] . '" id="auth' . $row['auth'] . '" required>
                    <label class="form-check-label" for="auth' . $row['id'] . '">' . $row['auth'] . '</label>
                  </div>';
            }
            ?>
        </div>

        <p></p>

        <div class="form-group border border-success p-1">
            <label><b>Seleziona il Ruolo:</b></label>
            <?php
            // Populate options dynamically from the 'gruppi' table
            $gruppiQuery = "SELECT * FROM gruppi";
            $gruppiResult = $connessione->query($gruppiQuery);

            while ($row = $gruppiResult->fetch_assoc()) {
                echo '<div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" name="gruppi" value="' . $row['gruppi'] . '" id="gruppo' . $row['gruppi'] . '" required>
                    <label class="form-check-label" for="gruppo' . $row['id'] . '">' . $row['gruppi'] . '</label>
                  </div>';
            }
            ?>
        </div>

        <p></p>

        <input type="hidden" name="memberOf" id="memberOf" value="">

        <div class="form-group border border-success p-1">
            <label><b>Opzioni Utente:</b></label>
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
        </div>

        <p></p>

        <!-- New Input: course (text) -->
        <div class="form-group border border-success p-1">
            <label for="course"><b>Course:</b></label>
            <input type="text" class="form-control" name="course" id="course">
        </div>

        <p></p>

        <!-- New Input: group (text) -->
        <div class="form-group border border-success p-1">
            <label for="group"><b>Group:</b></label>
            <input type="text" class="form-control" name="group" id="group">
        </div>

        <p></p>

        <div class="text-center">
            <button type="submit" class="btn btn-success" name="import">Conferma ed Elabora il File CSV</button>
        </div>
    </form>

    </div>

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

<script>
    // Function to update the value of the hidden field based on the selected radio button
    function updateMemberOfValue() {
        var selectedGruppi = document.querySelector('input[name="gruppi"]:checked');
        if (selectedGruppi) {
            var cnValue = selectedGruppi.value; // Value from the selected radio button
            var memberOfValue = 'CN=' + cnValue + ',OU=Gruppi,DC=cenforaven,DC=edu';
            document.getElementById('memberOf').value = memberOfValue;
        } else {
            // Handle the case where no radio button is selected
            document.getElementById('memberOf').value = '';
        }
    }

    // Attach the function to the change event of the radio buttons
    var radioButtons = document.querySelectorAll('input[name="gruppi"]');
    radioButtons.forEach(function (radioButton) {
        radioButton.addEventListener('change', updateMemberOfValue);
    });

    // Initial call to set the initial value
    updateMemberOfValue();
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

