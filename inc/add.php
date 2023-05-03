<?php


use MongoDB\BSON\ObjectId;

$mdb = new myDbClass();

$client = $mdb->getClient();
$movies_collection = $mdb->getCollection('movies');
$confirm = GETPOST('confirm_envoyer');
if ($confirm == 'Envoyer') {

    /**
     *  A implémenter : 
     * Récupérer les données transmieses par le formulaire
     * Les envoyer en tant que nouvel enregistrement dans votre base MongoDB
     * Si c'est OK : On retourne à la liste,
     * Si il y a eu une erreur, On reste sur la page d'ajout
     * */
    $title = GETPOST('title');
    $year = GETPOST('year');
    $realisateurs = GETPOST('realisateurs');
    $producteurs = GETPOST('producteurs');
    $acteurs_principaux = GETPOST('acteurs_principaux');
    $synopsis = GETPOST('synopsis');
    try {
        $movies_collection->insertOne(
            [
                'title' => $title,
                'year' => $year,
                'realisation' => $realisateurs,
                'production' => $producteurs,
                'actors' => $acteurs_principaux,
                'synopsis' => $synopsis,
            ]
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
    <h2>Ajout d'un nouvel element</h2>
</div>
<form class="w3-container" action="index.php?action=add" method="POST">
    <div class="dcontent">
        <div class="w3-row-padding">
            <div class="w3-half">
                <label class="w3-text-blue" for="title"><b>Titre</b></label>
                <input class="w3-input w3-border" type="text" id="title" name="title" />
            </div>
            <div class="w3-half">
                <label class="w3-text-blue" for="year"><b>Année de sortie</b></label><br />
                <input type="text" id="year" name="year" />
            </div>
        </div>
        <div class="w3-row-padding">
            <div class="w3-half">
                <label class="w3-text-blue" for="realisateurs"><b>Réalisateurs</b></label>
                <textarea class="w3-input w3-border" id="realisateurs" name="realisateurs"></textarea>
            </div>
            <div class="w3-half">
                <label class="w3-text-blue" for="producteurs"><b>Producteurs</b></label>
                <textarea class="w3-input w3-border" id="producteurs" name="producteurs"></textarea>
            </div>
        </div>
        <div class="w3-row-padding">
            <div class="w3-half">
                <label class="w3-text-blue" for="acteurs_principaux"><b>Acteurs Principaux</b></label>
                <textarea class="w3-input w3-border" id="acteurs_principaux" name="acteurs_principaux"></textarea>
            </div>
        </div>
        <label class="w3-text-blue" for="synopsis"><b>Synopsis</b></label>
        <textarea class="w3-input w3-border" id="synopsis" name="synopsis"></textarea>
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