<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jcalonso
 * Date: 08/04/2013
 * Time: 13:45
 * @author Juan Carlos Alonso <me@jcalonso.com>
 * Scrapper for the site mono-log.org
 */

// SimpleDom lib
require_once 'simple_html_dom.php';

// Search for the mp3Library database
define( 'JSON_MP3_LIB_PATH', 'monologMp3.json' );

// If the file exist load it as an array
if( file_exists( JSON_MP3_LIB_PATH ) ) {
    // Load it
    $mp3Library = json_decode( file_get_contents( JSON_MP3_LIB_PATH ), true );
    $mp3LibraryKeys = getMp3LibraryKeys( $mp3Library );
}
else{ // Or just create an array
    $mp3Library = array();
    $mp3LibraryKeys = array();
}

// Set the initial page
$pageUrl  = 'http://www.mono-log.org/blog';

do{
    // Get the html page
    $html = file_get_html( $pageUrl );

    // Load a page form the blog
    $actualPageArray = getAllMp3FromSinglePage( $html, $mp3LibraryKeys );

    // Check if we dont have a false
    if( $actualPageArray === false ){
        // finish the process
        break;
    }

    $mp3LibraryKeys = $actualPageArray['libraryKeys'];

    // Add the new results to the main array
    $mp3Library = array_merge( $actualPageArray['pageResults'] , $mp3Library );

    // Search for next page
    $pagination = $html->find('.pagination');
    $paginationLinks = $pagination[0]->find('a');

    $nextPage = false;

    // Search for pagination link to see if we have more pages to scrap.
    foreach( $paginationLinks as $pageLink ) {

        if( $pageLink->plaintext === 'Next Page' ) {

            $nextPage = $pageLink->href;

        }
    }

    // Mark the flag to search again
    if( $nextPage ) {

        $pageUrl = 'http://www.mono-log.org/blog' . $nextPage;
    }

    $counter++;

}
while( $nextPage );

// TODO: Order the array based on the key

// Write the file
file_put_contents( JSON_MP3_LIB_PATH, json_encode( $mp3Library ) );

echo json_encode( array( 'playlist' => $mp3Library ) );


function getAllMp3FromSinglePage( $simpleDomPage, $libraryKeys, $forceReindexing = false )
{
    $html = $simpleDomPage;

    $pageResults = array();

    // Loop trough each mp3 link
    foreach( $html->find('.entry') as $songPost ) {

        // Get the url
        $songLink = $songPost->find('.entry-title a');
        $url = str_replace(' ', '%20', $songLink[0]->href);

        // Get the number
        $number = $songLink[0]->plaintext;

        // Check that we don't have in our mp4 library database
        if( in_array( $number, $libraryKeys) ) {

            // If $forceReindexing is true, then it will check every single key
            if(  $forceReindexing )
            {
                continue;
            }
            else{
                // If not, only the first is checked and stops the process since the songs are read form newer
                // to older, if we found a key, means we already have all the older.
                return false;
            }

        }
        else{
            $libraryKeys[] = $number;
        }

        // Get the date
        $songDate = $songPost->find('.entry-date');
        $date = $songDate[0]->plaintext;

        // Get the post title referenced to this mp3
        $songTitle = $songPost->find('.sm-ll a');
        $postLink = $songTitle[0]->href;
        $postName = $songTitle[0]->plaintext;

        $pageResults[] = array( '0' => array('src' => $url, 'type' => 'audio/mp3' ),
                                '1' => array('src' => $url, 'type' => 'audio/ogg' ),
                                'config' => array(  'title'     => $number . ' - ' . $postName,
                                                    'post'      => 'http://mono-log.org' . $postLink,
                                                    'poster'    => 'http://mono-log.org/dev/assets/img/todays-desk.png',
                                                    'number'    => $number,
                                                    'date'      => $date  ),
                                );

    }

    return array( 'pageResults' => $pageResults, 'libraryKeys' => $libraryKeys );
}

function getMp3LibraryKeys( $mp3Library )
{
    $keys = array();

    foreach( $mp3Library as $entry )
    {
        $keys[] = $entry['config']['number'];
    }

    return $keys;
}

?>