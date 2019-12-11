<?php
$force = false;
if (isset($argv[1]) && $argv[1] == '-f') { // option pour forcer l'exécution
    $force = true;
}

/* lecture du fichier .ini */
if (file_exists('src/config/database.ini')) {
    $conf = parse_ini_file('src/config/database.ini');
} else {
    fwrite(STDERR, "Fichier 'database.ini' introuvable dans /src/config/\n");
    exit(1);
}

$driver = $conf['driver'];
$host = $conf['host'];
$database = $conf['database'];
$username = $conf['username'];
$password = $conf['password'];
$charset = $conf['charset'];
$collation = $conf['collation'];


/* création de la BDD */
try {
    $conn = new PDO("$driver:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // TODO set charset & collation from .ini
    $stmt = $conn->prepare("SHOW DATABASES LIKE '$database'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_NUM);

    if ($result) { // si la BDD existe deja
        echo "Attention la BDD '$database' existe déjà, continuer entrainera la suppression des données de cette BDD.\n";
        if (!$force) {
            echo "Entrez 'ok' pour continuer: ";
            $line = substr(stream_get_line(STDIN, 1024, "\n"),0,2);
            if ($line != 'ok') {
                echo "Annulation\n";
                exit(2);
            }
        }
        $conn->exec("DROP DATABASE $database");
    }
    $conn->exec("CREATE DATABASE IF NOT EXISTS $database");
    $conn->exec("USE $database");
    echo "La BDD '$database' à bien été initialisé.\n";
} catch (PDOException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(3);
}


/* création des tables */
$sql = file_get_contents('src/config/database_create.sql');
try {
    $conn->exec($sql);
    echo "Les tables ont bien été créées.\n";
} catch (PDOException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(4);
}


/* insertion des données */
if (!$force) {
    echo "Insérer les données d'exemple ? (o/n): ";
    $line = substr(stream_get_line(STDIN, 1024, "\n"),0,1);
}
if ($force || $line == 'o' || $line == 'y') {
    $sql = file_get_contents('src/config/database_insert.sql');
    try {
        $conn->exec($sql);
        echo "Les données ont bien été insérées.\n";
    } catch (PDOException $e) {
        fwrite(STDERR, $e->getMessage() . "\n");
        exit(5);
    }
}

$conn = null;
echo "Terminé\n";
exit(0);
?>