<!DOCTYPE html>
<html lang="sv">
<?php
// Följande definition kan med fördel ligga i en separat config-fil.
$upload_errors = array(
    // http://www.php.net/manual/en/features.file-upload.errors.php
    UPLOAD_ERR_OK          => "Inga fel.",
    UPLOAD_ERR_INI_SIZE    => "Filen är större än den storlek som anges i php.ini (upload_max_filesize).",
    UPLOAD_ERR_FORM_SIZE   => "Filen är större än den största filstorlek som angets i formuläret (MAX_FILE_SIZE).",
    UPLOAD_ERR_PARTIAL     => "Filen blev delvis uppladdad.",
    UPLOAD_ERR_NO_FILE     => "Ingen fil är vald.",
    UPLOAD_ERR_NO_TMP_DIR  => "Ingen temporär katalog finns på webbservern.",
    UPLOAD_ERR_CANT_WRITE  => "Kan inte skriva till disk.",
    UPLOAD_ERR_EXTENSION   => "Filuppladdningen är stoppad av ett tillägg (extension)."
);

// Om formuläret har skickats
if(isset($_POST['submit']))
{
    // Utskrift av $_FILES för att se vad den innehåller, tas bort när scriptet är klart!
    echo "<pre>";
    print_r($_FILES['file_upload']);
    echo "</pre><hr />";

    // Hantera formulärets data
    $tmp_file = $_FILES['file_upload']['tmp_name'];
    $upload_dir = "uploads/";
    $target_file = basename($_FILES['file_upload']['name']);

    // Glöm inte att kontrollera om filen redan finns,
    // och bestäm vad som ska hända om den gör det.
    // Använd php-funktionen file_exists() för att kontrollera
    // detta och utför sedan lämplig åtgärd om den finns.

    // Om move_uploaded_file returnerar true så gick uppladdningen bra
    if(move_uploaded_file($tmp_file, $upload_dir . $target_file))
    {
        $message = "Filen har laddats upp.";
    }

    // Något gick fel vid uppladdningen
    else
    {
        $error = $_FILES['file_upload']['error'];
        $message = $upload_errors[$error];
    }
}
?>
<head>
    <meta charset="UTF-8mb4">
    <title>Databas</title>
</head>
<body>
    <?php
        // Här skrivs meddelandet ut, så användaren får veta hur uppladdningen gick
        if(!empty($message))
            echo "<p>{$message}</p>";
    ?>
    <form enctype="multipart/form-data" method="POST">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="file" name="file_upload" />
        <input type="submit" name="submit" value="Upload" />
    </form>
</body>
</html>