<?php

use MongoDB\BSON\ObjectId;

$mdb = new myDbClass();

$client = $mdb->getClient();
$id = GETPOST('id');
if ($id == '') {
?>
    <div class="dtitle w3-container w3-teal">
        <h2>Cet élément n'a pas été trouvé</h2>
    </div>
<?php
} else {
    $obj_id = new MongoDB\BSON\ObjectId($id);
    $movies_collection = $mdb->getCollection('movies');
    $cursor = $movies_collection->find(
        ['_id' => $obj_id],
        ['limit' => 1],
    );

    $cursor->setTypeMap(array('root' => 'array', 'document' => 'array', 'array' => 'array'));
    $iterator = new IteratorIterator($cursor);

    $iterator->rewind();
    $cols = array(
        '_id' => array('lbl' => '#', 'type' => 'id'),
        'title' => array('lbl' => 'Titre', 'type' => 'text'),
        'year' => array('lbl' => 'Année', 'type' => 'text'),
        'production' => array('lbl' => 'Production', 'type' => 'array'),
        'actors' => array('lbl' => 'Acteurs', 'type' => 'array'),
        'synopsis' => array('lbl' => 'Synopsis', 'type' => 'textarea'),
    );
    // $iterator->next();
    if ($iterator->valid()) {
        $document = $iterator->current();
        $elt = secure_document($document, $cols);
    }

    $confirm = GETPOST('confirm_envoyer');
    if ($confirm == 'Envoyer') {

        /**
         *  A implémenter : 
         * Récupérer les données transmises par le formulaire
         * Les envoyer pour mettre à jour l'enregistrement correspondant dans votre base MongoDB
         * Si c'est OK : On retourne à la liste,
         * Si il y a eu une erreur, On reste sur la page d'édition
         * */

        $title = GETPOST('title');
        $year = GETPOST('year');
        $realisateurs = GETPOST('realisateurs');
        $production = GETPOST('production');
        $actors = GETPOST('actors');
        $synopsis = GETPOST('synopsis');
        
        try {
            $updateResult = $movies_collection->updateOne(
                ['_id' => $obj_id],
                ['$set' => [
                    'title' => $title,
                    'year' => $year,
                    'production' => [
                        'production' => $production
                    ],
                    'actors' => $actors,
                    'synopsis' => $synopsis
                ]]
            );
            header('Location: index.php?action=list');
        }
        catch (Exception $e) {
            print $e->getMessage();
            header('Location: index.php?action=add');
        }
    
        exit(0);
    }

?>
    <div class="dtitle w3-container w3-teal">
        <h2>Modification d'un element</h2>
    </div>
    <form class="w3-container" action="index.php?action=edit" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="dcontent">
            <div class="w3-row-padding">
                <div class="w3-half">
                    <label class="w3-text-blue" for="title"><b>Titre</b></label>
                    <input class="w3-input w3-border" type="text" id="title" name="title" value="<?php echo $elt['title']; ?>" />
                </div>
                <div class="w3-half">
                    <label class="w3-text-blue" for="year"><b>Année de sortie</b></label><br />
                    <input type="text" id="year" name="year" value="<?php echo $elt['year']; ?>" />
                </div>
            </div>
            <div class="w3-row-padding">
                <div class="w3-half">
                    <label class="w3-text-blue" for="actors"><b>Acteurs Principaux</b></label>
                    <textarea class="w3-input w3-border" rows=6 id="actors" name="actors"><?php echo implode(PHP_EOL, $elt['actors']); ?></textarea>
                </div>
                <div class="w3-half">
                    <label class="w3-text-blue" for="production"><b>Producteurs</b></label>
                    <textarea class="w3-input w3-border" rows=3 id="production" name="production"><?php echo implode(PHP_EOL, $elt['production']); ?></textarea>
                </div>
            </div>
            <div class="w3-row-padding">
                <div class="w3-full">
                    <label class="w3-text-blue" for="synopsis"><b>Synopsis</b></label>
                    <textarea class="w3-input w3-border" rows=10 id="synopsis" name="synopsis"><?php echo nl2br($elt['synopsis']); ?></textarea>
                </div>
            </div>
            <br />
            <div class="w3-row-padding">
                <div class="w3-half">
                    <input class="w3-btn w3-red" type="submit" name="cancel" value="Annuler" />
                </div>
                <div class="w3-half">
                    <input class="w3-btn w3-blue-grey" type="submit" name="confirm_envoyer" value="Envoyer" />
                </div>
            </div>
            <br /><br />
    </form>
    </div>
    <div class="dfooter">
    </div>
<?php
}
