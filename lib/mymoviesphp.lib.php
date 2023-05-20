<?php

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use MongoDB;

function GETPOST($paramname)
{
    if (isset($_POST[$paramname]))
        return $_POST[$paramname];
    if (isset($_GET[$paramname]))
        return $_GET[$paramname];
    return false;
}

function GETPOSTISSET($paramname)
{
    return (isset($_POST[$paramname]) || isset($_GET[$paramname]));
}


function print_tr_movie($document, $cols)
{
    $elt = secure_document($document, $cols);
    print '<tr>';
    foreach ($cols as $key => $dtls) { ?>
        <td>
            <?php
            if ($dtls['type'] == 'id') {
                echo '<a href="index.php?action=delete&id=' . ($elt[$key] ?? '') . '">';
                echo '<i class="fas fa-trash w3-hover-opacity" aria-hidden="true"></i>';
                echo '</a>';
                echo '<a href="index.php?action=edit&id=' . ($elt[$key] ?? '') . '">';
                echo '<i class="fas fa-edit w3-hover-opacity" aria-hidden="true"></i>';
                echo '</a>';
            } elseif ($dtls['type'] == 'textarea') {
                print nl2br($elt[$key]);
            } elseif ($dtls['type'] == 'array') {
                print implode('<br />', $elt[$key]);
            } else {
                echo $elt[$key];
            }
            ?>
        </td>
<?php
}
//     print '<td>';
//     echo '<a href="index.php?action=delete&id=' . $elt[$key] . '">';
//     echo '<i class="fas fa-trash w3-hover-opacity" aria-hidden="true"></i>';
//     echo '</a>';
//     print '</td>';
//     print '</tr>';
}

function merge_dtls($doc, $dtls, $cast)
{
    $list_prod = $dtls['production_companies'];
    $nb_prod = sizeof($list_prod);
    $final_prods = array();
    for ($i = 0; $i < $nb_prod; $i++) {
        $final_prods[] = $list_prod[$i]['name'];
    }
    $doc['production'] = implode(PHP_EOL, $final_prods);
    $doc['_id'] = isset($doc['_id']) ? (string)$doc['_id'] : '';

    $list_cast = $cast['cast'];
    $nb_prod = sizeof($list_cast);
    $final_cast = array();
    for ($i = 0; $i < min($nb_prod, 5); $i++) {
        $final_cast[] = $list_cast[$i]['name'];
    }
    $doc['production'] = implode(PHP_EOL, $final_prods);
    $doc['actors'] = implode(PHP_EOL, $final_cast);
    return $doc;
}

function secure_document($elt, $cols)
{
    //    print_r($elt);
    foreach ($elt as $i_elt => $val_elt) {
        if (is_object($val_elt)) {
            $classname = get_class($val_elt);
            switch ($classname) {
                case 'MongoDB\BSON\ObjectId':
                    $elt[$i_elt] = $val_elt->__toString();
                    // var_dump($elt[$i_elt]);
                    break;
                case 'MongoDB\Model\BSONArray':
                    $elt[$i_elt] = (array)$val_elt;
                    // var_dump($elt[$i_elt]);
                    break;
                default;
                    // print 'Obj : '.var_dump($classname) . '<br />';
                    break;
            }
        } else {
            //          print 'Oth : '.gettype($val_elt) . '<br />';
        }
    }
    // print '<hr />';
    // print_r($elt);
    // print '<hr />';
    foreach ($cols as $key => $dtls) {
        switch ($dtls['type']) {
            case 'array':
                if (!isset($elt[$key]))
                    $elt[$key] = array();
                $elt[$key] = (is_array($elt[$key]) ? $elt[$key] : (array($elt[$key])));
                break;
            case 'textarea':
                if (!isset($elt[$key]))
                    $elt[$key] = '';
                break;
            default:
                break;
        }
    }
    return $elt;
}
