<?php
require 'database.php';

if (isset($_POST["download_csvWindows"])) {
    $csvContent = "givenName,sn,commonName,displayName,name,userPrincipalName,sAMAccountName,userAccountControl,memberOf\n";

    $sql = "SELECT * FROM utenti";
    if ($result = mysqli_query($connessione, $sql)) {
        while ($row = mysqli_fetch_array($result)) {
            $nome = ucwords(strtolower($row['nome']));
            $cognome = strtoupper($row['cognome']);
            $commonName = $nome . " " . $cognome;
            $displayName = $commonName;
            $name = $commonName;
            $userPrincipalName = $row['email'];
            $sAMAccountName = substr($userPrincipalName, 0, strpos($userPrincipalName, '@'));
            $userAccountControl = $row['userAccountControl'];;
            $memberOf = $row['memberOf']; // Add this line to retrieve the 'memberOf' value

            $csvContent .= "$nome,$cognome,$commonName,$displayName,$name,$userPrincipalName,$sAMAccountName,$userAccountControl,\"$memberOf\"\n";
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="exportWINDOWS.csv"');

        // Output CSV content
        echo $csvContent;

        // Terminate script after CSV download
        exit;
    }
}

if (isset($_POST["download_csvMoodle"])) {
    $csvContent = "firstname;lastname;idnumber;email;username;auth;course;group\n";

    $sql = "SELECT * FROM utenti";
    if ($result = mysqli_query($connessione, $sql)) {
      while ($row = mysqli_fetch_array($result)) {
        $nome = ucwords(strtolower($row['nome']));
        $cognome = strtoupper($row['cognome']);
        $idnumber = strtoupper($row['idnumber']);
        $email = $row['email'];
        $auth = $row['auth'];
        $course = strtoupper($row['course']);
        $group = strtoupper($row['group']);
    
        // Modifica per ottenere solo la parte prima della chiocciola come username
        $username = substr($email, 0, strpos($email, '@'));
    
        $csvContent .= "$nome;$cognome;$idnumber;$email;$email;$auth;$course;$group\n";
    }
    

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="exportMOODLE.csv"');

        // Output CSV content
        echo $csvContent;

        // Terminate script after CSV download
        exit;
    }
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

<div class="right-container text-center"> 

<p></p>

<h2 style="color: red;">"Elaborazione file CSV avvenuto con successo"</h2><br>
<h5><em>Clicca sui pulsanti qui sotto per esportare il file CSV nel formato desiderato</em></h5>

<!-- Pulsanti con immagini per scaricare il CSV -->
<form method="post" class="mb-3">
    <button type="submit" class="btn btn-outline-primary me-2" name="download_csvWindows">
        <img src="img/icona AD.png" alt="Scarica CSV Formato Windows" style="width: 40px; height: 40px; margin-right: 5px;">Scarica CSV Formato Windows
    </button>
    <button type="submit" class="btn btn-outline-warning" name="download_csvMoodle">
        <img src="img/icona Moodle.png" alt="Scarica CSV Formato MOODLE" style="width: 40px; height: 40px; margin-right: 5px;">Scarica CSV Formato MOODLE
    </button>
</form>

<style>
    .my-table th,
    .my-table td {
        font-size: 18px;
    }
</style>
        
<p></p>

    <!-- Barra di ricerca -->
    <div class="mb-3">
        <input type="text" class="form-control" id="search" placeholder="Ricerca Rapida Nominativo" autocomplete="off">
    </div>
        
        <?php
          
          $sql = "SELECT * FROM utenti";
          if ($result = mysqli_query($connessione, $sql)) {
            $x = 1;
            if (mysqli_num_rows($result) > 0) {
              echo '<table id="dataTable" class="table my-table table-striped" style="width:100%">';
              echo "<thead>";
              echo "<tr>";
              echo "<th>";
              echo "<th>Nome</th>";
              echo "<th>Cognome</th>";
              echo "<th>Email</th>";
              echo "<th>Gruppo</th>";
              echo "<th>IdNumber</th>";
              echo "<th>Course</th>";
              echo "<th>Group</th>";
              echo "<th>Tipo Auth</th>";
              echo "<th>Codice Opzioni</th>";
              echo "<th></th>";
              echo "</tr>";
              echo "</thead>";
              echo "<tbody id=tableBody>";
              while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>$x</td>";
                $x++;
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . $row['cognome'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>
                <select class='form-select' name='groupDropdown' onchange='updateGroup(this, {$row["id"]})'>";
            
            // Esegui una query per ottenere i dati univoci dalla tabella "gruppi"
            $groupQuery = "SELECT DISTINCT gruppi FROM gruppi";
            $groupResult = mysqli_query($connessione, $groupQuery);

            
            while ($groupRow = mysqli_fetch_assoc($groupResult)) {
                $selected = ($row['gruppi'] == $groupRow['gruppi']) ? 'selected' : '';
                echo "<option value='{$groupRow['gruppi']}' $selected>{$groupRow['gruppi']}</option>";
            }
            
            echo "</select></td>";
            
                echo "<td>" . $row['idnumber'] . "</td>";
                echo "<td>" . $row['course'] . "</td>";
                echo "<td>" . $row['group'] . "</td>";
                echo "<td>
                <select class='form-select' name='authDropdown' onchange='updateAuth(this, {$row["id"]})'>";
            
            // Esegui una query per ottenere i dati univoci dalla tabella "auth"
            $authQuery = "SELECT DISTINCT auth FROM auth";
            $authResult = mysqli_query($connessione, $authQuery);

            
            while ($authRow = mysqli_fetch_assoc($authResult)) {
                $selected = ($row['auth'] == $authRow['auth']) ? 'selected' : '';
                echo "<option value='{$authRow['auth']}' $selected>{$authRow['auth']}</option>";
            }
            
            echo "</select></td>";
            echo "<td>";
            echo $row['userAccountControl'];
            echo ' <a href="modificaCodiceOpzioni.php?id=' . $row["id"] . '"><i class="fa fa-edit" title="Modifica Codice Opzioni" style="font-size: 15px; color: green;"></i></a>';
            echo "</td>";
                echo "<td>";
                echo '<a href="delete.php?id=' . $row["id"] . '" onclick="return confirm(\'Sei sicuro di voler eliminare questo utente?\')"><i class="fa fa-trash-o" title="Elimina" style="font-size: 20px; color: red;"></i></a>
                </td>';
                echo "</tr>";

              }
              echo "</tbody>";
              echo "</table>";
              // Free result set
              mysqli_free_result($result);
            } else {
              echo '<div class="alert alert-danger"><em>Nessun Frequentatore Presente nel Database</em></div>';
            }
          } else {
            echo "Oops! Something went wrong. Please try again later.";
          }

          // Close connection
          mysqli_close($connessione);
          ?>

        </div>

<!-- Script per la funzione di ricerca su colonne "Nome" e "Cognome" -->
<script>
    document.getElementById('search').addEventListener('input', function () {
        let input, filter, table, tr, td, i, txtValue;
        input = document.getElementById('search');
        filter = input.value.toUpperCase();
        table = document.getElementById('tableBody');
        tr = table.getElementsByTagName('tr');

        for (i = 0; i < tr.length; i++) {
            let cognomeColumn = tr[i].getElementsByTagName('td')[2].textContent || tr[i].getElementsByTagName('td')[2].innerText;
            let nomeColumn = tr[i].getElementsByTagName('td')[3].textContent || tr[i].getElementsByTagName('td')[3].innerText;
            let ufficioColumn = tr[i].getElementsByTagName('td')[4].textContent || tr[i].getElementsByTagName('td')[4].innerText;
            let telefonoColumn = tr[i].getElementsByTagName('td')[5].textContent || tr[i].getElementsByTagName('td')[5].innerText;

            if (nomeColumn.toUpperCase().indexOf(filter) > -1 || cognomeColumn.toUpperCase().indexOf(filter) > -1 || ufficioColumn.toUpperCase().indexOf(filter) > -1 || telefonoColumn.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    });
</script>


  <!-- End page content -->
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
function updateGroup(selectElement, rowId) {
    var selectedValue = selectElement.value;

    // Esegui la richiesta AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_gruppo.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Puoi gestire la risposta dal server qui se necessario
            console.log(xhr.responseText);
        }
    };

    // Invia i dati al server
    xhr.send("rowId=" + rowId + "&selectedValue=" + selectedValue);
}
</script>

<script>
function updateAuth(selectElement, rowId) {
    var selectedValue = selectElement.value;

    // Esegui la richiesta AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "update_auth.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Puoi gestire la risposta dal server qui se necessario
            console.log(xhr.responseText);
        }
    };

    // Invia i dati al server
    xhr.send("rowId=" + rowId + "&selectedValue=" + selectedValue);
}
</script>

<!-- // Funzione per tornare in alto della pagina -->
<script>
    function scrollToTop() {
        document.body.scrollTop = 0; // Per Safari
        document.documentElement.scrollTop = 0; // Per Chrome, Firefox, IE e Opera
    }

    // Aggiungi un evento al caricamento del documento per inizializzare l'ascoltatore di eventi
    document.addEventListener("DOMContentLoaded", function () {
        // Aggiungi un ascoltatore di eventi al documento
        document.addEventListener("scroll", function () {
            // Ottieni l'elemento del pulsante "Torna su"
            var scrollToTopButton = document.getElementById("scrollToTopButton");

            // Mostra o nascondi il pulsante in base alla posizione dello scroll
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                scrollToTopButton.style.display = "block";
            } else {
                scrollToTopButton.style.display = "none";
            }
        });
    });
</script>

<!-- Pulsante Torna su -->
<style>
        #scrollToTopButton {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #198754;
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 10px 15px;
            cursor: pointer;
        }
    </style>

    <button id="scrollToTopButton" onclick="scrollToTop()" title="Torna su">
        <i class="fa fa-arrow-up"></i>
    </button>

</body>
</html>