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
        $password = $row[4];
        $gruppo = $row[5];
        $docente = $row[6];

        // Insert new data
        $insertStmt = $connessione->prepare("INSERT INTO frequentatori (cognome, nome, email, password, gruppo, docente) VALUES (?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssss", $cognome, $nome, $email, $password, $gruppo, $docente);
        $insertStmt->execute();
        $insertStmt->close();
    }

    // Commit the transaction
    $connessione->commit();
    $connessione->autocommit(TRUE); // Re-enable autocommit


}
?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../CSS/style2023.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- SweetAlert2 modal-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

  <!-- Include la libreria JavaScript di Bootstrap e jQuery -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <!-- Ricerca Tabella -->
  <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

  <title>Frequentatori</title>
          <link rel="icon" type="image/x-icon" href="../media/logoPPF.png" />
    
</head>

<body>


    <!-- Header -->
<header>
  <div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
    <a class="w3-bar-item w3-left" href="#" style="color:white; font-size:22px; text-decoration:none;"><i class="fa-solid fa fa-map-signs"></i> Frequentatori</a>
  </div>
</header>

<style>
        .my-table th,
        .my-table td {
            font-size: 20px;
        }
    </style>

<div class="main" style="margin-top:50px;">
<div class="right-container text-center"> 
  <div class="container mt-5">

<h1>Carica e Converti Elenco Frequentatori Loreto</h1>
<a href="CSV-fac-simile.csv">(Clicca qui per scaricare il modello Fac-Simile da compilare e caricare nel database)</a>
<p></p>

<!-- modulo di caricamento del file -->
<form action="" method="post" enctype="multipart/form-data">
  <label for="csv_file" class="form-label">Inserisci file CSV:
  <input type="file" class="form-control" name="csv_file" accept=".csv" required></label>
<button type="submit" class="btn btn-sm btn-primary" name="import">Inserisci</button>

        </form>

<!-- Form per il download CSV e pulizia tabella -->
<form action="" method="post">

    <!-- Pulsante per eliminare tutti i dati dalla tabella -->
    <button type="submit" class="btn btn-danger" name="delete_all">Pulisci Tabella</button>
    
    <!-- Pulsante per scaricare il CSV -->
    <button type="submit" class="btn btn-success" name="download_csv">Scarica CSV Formato WINDOWS SERVER</button>

</form>


        
<p></p>
        
        <?php


          // Tabella
          
          $sql = "SELECT * FROM frequentatori";
          if ($result = mysqli_query($connessione, $sql)) {
            $x = 1;
            if (mysqli_num_rows($result) > 0) {
              echo '<table id="dataTable" class="table my-table table-striped" style="width:100%">';
              echo "<thead>";
              echo "<tr>";
              echo "<th>";
              echo "<th>Cognome</th>";
              echo "<th>Nome</th>";
              echo "<th>Email</th>";
              echo "<th>Password</th>";
              echo "<th>Gruppo</th>";
              echo "<th>Docente</th>";
              echo "<th></th>";
              echo "<th></th>";
              echo "</tr>";
              echo "</thead>";
              echo "<tbody id=tableBody>";
              while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>$x</td>";
                $x++;
                echo "<td>" . $row['cognome'] . "</td>";
                echo "<td>" . $row['nome'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['password'] . "</td>";
                echo "<td>" . $row['gruppo'] . "</td>";
                echo "<td>" . $row['docente'] . "</td>";
                echo "<td>";
                echo '<a href="updateFrequentatore.php?id_utente=' . $row["id"] . '"><i class="fa fa-pencil" title="Modifica" style="font-size: 20px; color: green;"></i></a> ';
                echo "</td>";
                echo "<td>";
                echo '<a href="deleteFrequentatore.php?id_utente=' . $row["id"] . '" onclick="return confirm(\'Sei sicuro di voler eliminare questo utente?\')"><i class="fa fa-trash-o" title="Elimina" style="font-size: 20px; color: red;"></i></a>
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




  <!-- End page content -->
</div>

<style>
  footer {
  background-color: black;
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
