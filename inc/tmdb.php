<?php
require_once(dirname(__FILE__) . '/../class/tmdb.class.php');
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use MongoDB\BSON\ObjectId;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__) . '/../');
$dotenv->load();
$api_key = $_ENV['TMDB_APIKEY'];

// Set-up the class with your own language
$tmdb = new TMDb($api_key, 'fr', TRUE);

$import = GETPOST('import') ? GETPOST('import') : '';
$forceUpdate = GETPOST('forceUpdate') ? GETPOST('forceUpdate') : '';
$page = GETPOST('num_page') ? GETPOST('num_page') : 1;
$with_people = GETPOST('with_people') ? GETPOST('with_people') : 2710;
// After initialize the class
// First request a token from API
$token = $tmdb->getAuthToken();
// Request valid session for that particular user from API
// $session = $tmdb->getAuthSession();

//Retrieve config when the class is already initialised from TMDb (always new request)
$config = $tmdb->getConfiguration();

$response = $tmdb->discoverMovie(array('with_people' => $with_people, 'language' => 'fr-FR'), $page);

$results = $response['results'];
$nb_pages = $response['total_pages'];
$params = '';
if ($with_people)
    $params .= '&with_people=' . $with_people;
if ($import)
    $params .= '&import=' . $import;
if ($forceUpdate)
    $params .= '&forceUpdate=' . $forceUpdate;

$cols = array(
    '_id' => array('lbl' => '#', 'type' => 'id'),
    'title' => array('lbl' => 'Titre', 'type' => 'text'),
    'year' => array('lbl' => 'Année', 'type' => 'text'),
    'production' => array('lbl' => 'Production', 'type' => 'array'),
    'actors' => array('lbl' => 'Acteurs', 'type' => 'array'),
    'synopsis' => array('lbl' => 'Synopsis', 'type' => 'textarea'),
    'id_tmdb' => array('lbl' => 'TMDB', 'type' => 'text'),
);
// print '<pre>';
// print_r($results);
// print '</pre>';

?>
<div class="dmorehtmlright w3-third w3-left">
<form action="index.php?action=tmdb" method="post">
        <label for="with_people">ID TMDB à rechercher</label>
        <input type="text" name="id_tmdb" value="<?php echo $with_people; ?>" />
        <button type="submit">Ajouter</button>
    </form>
</div>
<div class="dtitle w3-half w3-left">Liste des elements</div>
<div class="dmorehtmlright w3-third w3-right">
    
    <?php
    if ($nb_pages <= 5) {
        for ($i_page = 1; $i_page <= $nb_pages; $i_page++) {
            print '<a href="index.php?action=tmdb' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
        }
    } else {

        for ($i_page = 1; $i_page <= 5; $i_page++) {
            print '<a href="index.php?action=tmdb' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
            
        }
        print '...&nbsp;';
        if ($nb_pages > 10 && $num_page > 2 && $num_page < ($nb_pages - 2)) {
            for ($i_page = $num_page - 5; $i_page <= $num_page + 5; $i_page++) {
                print '<a href="index.php?action=tmdb' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
            }
            print '...';
        }
        for ($i_page = $nb_pages - 5; $i_page <= $nb_pages; $i_page++) {
            print '<a href="index.php?action=tmdb' . $params . '&num_page=' . ($i_page) . '">' . ($i_page) . '</a>&nbsp;';
        }
    }
    ?>
</div>
<div class="dcontent w3-container">
    <table class="w3-table w3-striped">
        <tr>
            <?php
            foreach ($cols as $key => $dtls) {
            ?>
                <th><?php echo $dtls['lbl']; ?></th>
            <?php
            }
            ?>
        </tr>
        <?php
        $nb_res_on_page = sizeof($results);
        for ($i = 0; $i < $nb_res_on_page; $i++) {
            $current_res = $results[$i];

            $mdb = new myDbClass();

            $client = $mdb->getClient();
            $movies_collection = $mdb->getCollection('movies');
            $cursor = $movies_collection->find(
                ['id_tmdb' => $current_res['id']],
                ['limit' => 1],
            );
            $iterator = new IteratorIterator($cursor);

            $iterator->rewind();
            if ($iterator->valid()) {
                $documentmdb = $iterator->current();

                $document['_id'] = (string)$documentmdb['_id'];
                $document['id_tmdb'] = (string)$documentmdb['id_tmdb'];
            }
            $document['title'] = $current_res['title'];
            list($document['year'], $null) = explode('-', $current_res['release_date'], 2);
            $document['synopsis'] = $current_res['overview'];
            $document['id_tmdb'] = $current_res['id'];
            $dtls_film = $tmdb->getMovie($current_res['id']);
            $cast_film = $tmdb->getMovieCast($current_res['id']);
            $document = merge_dtls($document, $dtls_film, $cast_film);
            // if ($current_res['id'] == 1593) {
            //     print '<pre>';
            //     print_r($cast_film);
            //     print '</pre>';
            // }

            if ($import == 'confirm') {
                if ($current_res['id'] != $document['id_tmdb'] || $forceUpdate) {
                    /**
                     *  A implémenter : 
                     * Récupérer les données transmises par le formulaire
                     * Les envoyer pour mettre à jour l'enregistrement correspondant dans votre base MongoDB
                     * Si nous sommes sur un enregistrement déjà existant alors on fait une mise à jour,
                     * Sinon, c'est un nouvel enregistrement, alors on fait une création
                     * */
                    $submittedTmdbId = $GETPOST['id_tmdb'];

                    // Vérifiez si le film existe déjà dans la base de données
                    $existingMovie = $movies_collection->findOne(['id_tmdb' => $submittedTmdbId]);
                
                    if ($existingMovie) {
                        $updateResult = $movies_collection->updateOne(
                            ['id_tmdb' => $submittedTmdbId],
                            ['$set' => ['title' => $title, 'year' => $year, 'synopsis' => $synopsis]]
                        );
                    } else {
                        $newMovie = [
                            'id_tmdb' => $submittedTmdbId,
                            'title' => $title,
                            'year' => $year,
                            'production' => $production,
                            'actors' => $actors,
                            'synopsis' => $synopsis
                        ];
                        $insertResult = $movies_collection->insertOne($newMovie);
                    }
                }
            }
            print_tr_movie($document, $cols);
            unset($document);
        }