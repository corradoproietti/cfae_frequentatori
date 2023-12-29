<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idUtente = mysqli_real_escape_string($connessione, $_POST['id']);

    // Esegui la query per ottenere i dati dell'utente
    $query = "SELECT * FROM utenti WHERE id = $idUtente";
    $result = mysqli_query($connessione, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Mostra i checkbox basati sui dati dell'utente
        echo '<div class="form-group">';
        echo '<label>User Account Control:</label>';
        // Popola dinamicamente gli option dalla tabella 'userAccountControl'
        $userAccountControlQuery = "SELECT * FROM useraccountcontrol";
        $userAccountControlResult = $connessione->query($userAccountControlQuery);

        while ($userAccountControlRow = $userAccountControlResult->fetch_assoc()) {
            $checked = in_array($userAccountControlRow['codice'], explode(',', $row['userAccountControl'])) ? 'checked' : '';
            echo '<div class="form-check">
                    <input type="checkbox" class="form-check-input" name="userAccountControl[]" value="' . $userAccountControlRow['codice'] . '" id="userAccountControl' . $userAccountControlRow['codice'] . '" ' . $checked . '>
                    <label class="form-check-label" for="userAccountControl' . $userAccountControlRow['codice'] . '">' . $userAccountControlRow['nome'] . '</label>
                  </div>';
        }
        echo '</div>';
    } else {
        echo 'Errore nel recupero dei dati dell\'utente.';
    }
} else {
    echo 'Richiesta non valida.';
}
?>
