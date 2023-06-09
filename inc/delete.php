<?php

use MongoDB\BSON\ObjectId;

$mdb = new myDbClass();
$client = $mdb->getClient();
$movies_collection = $mdb->getCollection('movies');
$id = $_GET['id'] ?? '';

if ($id === '') {
?>
    <div class="dtitle w3-container w3-teal">
        <h2>Erreur</h2>
    </div>
<?php
} else {
    $obj_id = new MongoDB\BSON\ObjectId($id);

    $deleteResult = $movies_collection->deleteOne(['_id' => $obj_id]);

    if ($deleteResult->getDeletedCount() === 1) {
        header('Location: index.php?action=list');
    } else {
        ?>
        <div class="dtitle w3-container w3-teal">
            <h1>Erreur de suppression</h1>
        </div>
        <?php
    }
}
