<?php
require 'database.php';

// Verifica se il modulo di correzione è stato inviato
if (isset($_POST["correggi"])) {
// Ciclo attraverso tutte le righe inviate e eseguo l'aggiornamento nel database
foreach ($_POST["correggi"] as $index => $correzione) {
    $user_id = $correzione["user_id"];
    $new_email = $correzione["new_email"];

    // Effettua l'aggiornamento della email nella tabella utenti
    $updateQuery = "UPDATE utenti SET email = ? WHERE id = ?";
    $updateStmt = $connessione->prepare($updateQuery);
    $updateStmt->bind_param("si", $new_email, $user_id);
    $updateStmt->execute();

    if ($updateStmt->error) {
        die("Aggiornamento fallito: " . $updateStmt->error);
    }

    // Chiudi la dichiarazione preparata per l'aggiornamento
    $updateStmt->close();
}

// Reindirizza a esportaCSV.php dopo l'aggiornamento
header("Location: esportaCSV.php");
exit;
}

// Recupera i dati degli utenti con email non corrette
$query = "SELECT * FROM utenti WHERE email NOT LIKE '%@%'"; // Aggiungi la tua condizione specifica per identificare email non corrette
$result = $connessione->query($query);

if (!$result) {
    die("Esecuzione fallita: " . $connessione->error);
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

<style>
    .my-table th,
    .my-table td {
        font-size: 18px;
    }
</style>

<body>

    <div class="container-fluid bg-success text-white text-center">
        <h1><a href="index.php" style="color: inherit; text-decoration: none;">
        <i class="fa-solid fa fa-users"></i> Portale Inserimento Utenti AD & MOODLE</a></h1>
    </div>

    <div class="right-container text-center"> 
    <h2 class="text-center mb-4">ATTENZIONE:<br>il formato delle seguenti mail non è corretto</h2>
    <p class="text-center">Prima di proseguire bisogna correggere i seguenti indirizzi email:</p>
    
    <form method="post">
        <div class="table-responsive">
            <table class="table my-table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Formato Email Errato</th>
                        <th>Inserisci Email Corretta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>
                                <td>' . $row['nome'] . '</td>
                                <td>' . $row['cognome'] . '</td>
                                <td>' . $row['email'] . '</td>
                                <td>
                                    <input type="hidden" name="correggi[' . $row['id'] . '][user_id]" value="' . $row['id'] . '">
                                    <input type="email" name="correggi[' . $row['id'] . '][new_email]" placeholder="Nuova Email" required>
                                </td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn btn-success">Conferma le modifiche e procedi con l'elaborazione del file CSV</button>
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
    // Funzione che reindirizza alla pagina esportaCSV
    function redirectToEsportaCSV() {
        window.location.href = "esportaCSV.php";
    }

    // Aggiungi l'evento di reindirizzamento dopo il submit del form
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.querySelector('form');
        form.addEventListener('submit', function() {
            redirectToEsportaCSV();
        });
    });
</script>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
