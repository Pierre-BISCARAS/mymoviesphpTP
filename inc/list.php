<?php

use MongoDB\Collection;

$page = GETPOST('num_page') ? GETPOST('num_page') : 1;
$pagination = 25;
$params = '';
$arr_search = array();
$search_title = GETPOST('search_title');





    /**
     *  A implémenter : 
     * Récupérer les données reçues par le formulaire et
     * Construire le tableau de filtre qui sera utilisé dans la recherche 
     * envoyé à votre base MongoDB
     * */
    
$search_title = GETPOST('search_title');
if ($search_title)
    $arr_search['title'] = array(
        '$regex' => $search_title, 
        '$options' => 'i'
    );

$search_year = GETPOST('search_year');
if ($search_year)
    $arr_search['year'] = array(
        '$in' => $search_year(
            ($search_title),
            (int)($search_year)
        )
    );

$search_production = GETPOST('seach_production');
if ($search_production)
    $arr_search['production'] = array(
        '$regex' => $search_production, 
        '$options' => 'i'
    );

$search_actors = GETPOST('search_actors');
if ($search_actors)
    $arr_search['actors'] = array(
        '$regex' => $search_actors, 
        '$options' => 'i'
    );

$search_synopsis = GETPOST('search_synopsis');
if ($search_synopsis)
    $arr_search['synopsis'] = array(
        '$regex' => $search_production, 
        '$options' => 'i'
    );

$search_id_tmdb = GETPOST('search_id_tmdb');
if ($search_id_tmdb)
    $search_id_tmdb['id_tmdb'] = array(
        '$in' => $search_id_tmdb(
            ($search_id_tmdb),
            (int)($search_id_tmdb)
        )
    );

$mdb = new myDbClass();

$client = $mdb->getClient();
// $collections = $mdb->getCollections(); 
// foreach ($collections as $item) {
//     print $item['name'].'<br />';
//  };

$movies_collection = $mdb->getCollection('movies');
// print '<pre>';
// print_r($movies_collection);
// print '</pre>';
// $movies_collection = new Collection();
// print '<pre>';
// print_r($arr_search);
// print '</pre>';

$action = isset($GETPOST['action']) ? $GETPOST['action'] : '';
$id = isset($GETPOST['id']) ? $GETPOST['id'] : '';


if ($GETPOST['action'] === 'delete' && isset($GETPOST['id'])) {
    $documentId = $GETPOST['id'];

    // Vérifier si l'ID est valide
    if (MongoDB\BSON\ObjectId::isValid($documentId)) {
        // Supprimer le document de la collection "movies" en utilisant son ID
        $result = $movies_collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($documentId)]);

        // Vérifier si la suppression a réussi
        if ($result->getDeletedCount() > 0) {
            echo 'Le document a été supprimé avec succès.';
        } else {
            echo 'Impossible de trouver le document spécifié ou une erreur s\'est produite lors de la suppression.';
        }
    } else {
        echo 'ID invalide.';
    }
}



$nb_elts = $movies_collection->countDocuments($arr_search);
$nb_pages = ceil($nb_elts / $pagination);
$cursor = $movies_collection->find(
    $arr_search,
    [
        'sort' => ['year' => -1],
        'skip' => ($page > 0 ? (($page - 1) * $pagination) : 0),
        'limit' => $pagination,
    ]
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
    'id_tmdb' => array('lbl' => 'TMDB', 'type' => 'text'),
);

?>
<div class="dtitle w3-third w3-left">Liste des elements (<?php echo $nb_elts; ?>)</div>
<div class="daddnew w3-third w3-center"><a href="./index.php?action=add">Ajouter un element</a></div>
<div class="dmorehtmlright w3-third w3-right">
    <?php
    if ($nb_pages <= 5) {
        for ($i_page = 1; $i_page <= $nb_pages; $i_page++) {
            print '<a href="index.php?action=list' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
        }
    } else {

        for ($i_page = 1; $i_page <= 5; $i_page++) {
            print '<a href="index.php?action=list' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
        }
        print '...&nbsp;';

        print '<a href="index.php?action=list' . $params . '&num_page=' . ($page - 1) . '">' . ' < ' . '</a>&nbsp;';
        print $page . '&nbsp;';
        print '<a href="index.php?action=list' . $params . '&num_page=' . ($page + 1) . '">' . ' > ' . '</a>&nbsp;';
        print '...&nbsp;';

        for ($i_page = $nb_pages - 5; $i_page <= $nb_pages; $i_page++) {
            print '<a href="index.php?action=list' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
        }
    }
    ?>
</div>
<div class="dcontent w3-container">
    <table class="w3-table w3-striped">
        <thead>
            <tr>
                <?php
                foreach ($cols as $key => $dtls) {
                ?>
                    <th><?php echo $dtls['lbl']; ?></th>
                <?php
                }
                ?>
            </tr>
            <form name="searchForm" class="w3-container" action="index.php?action=list" method="GET">
                <tr>
                    <?php
                    foreach ($cols as $key => $dtls) {
                    ?>
                        <th>
                            <?php
                            switch ($key) {
                                case '_id':
                            ?>
                                    <a href="javascript: submitSearchForm();">
                                        <i class="fas fa-search w3-hover-opacity" aria-hidden="true"></i>
                                    </a>
                                <?php
                                    break;
                                default:
                                ?>
                                    <input type="text" name="search_<?php echo $key; ?>" value="<?php echo GETPOST('search_' . $key); ?>" />
                            <?php
                                    break;
                            }
                            ?>
                        </th>
                    <?php
                    }
                    ?>
                    <th>
                    </th>
                </tr>
            </form>
        </thead>
        <tbody>
            <?php
            // foreach ($cursor as $document) {
            //     print print_tr_movie($document);
            // }
            while ($iterator->valid()) {
                $document = $iterator->current();

                // print '<pre>';
                // print_r($document);
                // print '</pre>';
                // print '<br />';

                print_tr_movie($document, $cols);

                $iterator->next();
            }

            ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    function submitSearchForm() {
        document.searchForm.submit();
    }
</script>