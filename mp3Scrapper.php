<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jcalonso
 * Date: 08/04/2013
 * Time: 13:45
 * To change this template use File | Settings | File Templates.
 */

// SimpleDom lib
require_once 'simple_html_dom.php';

$mp3Library = array();

// Set the initial page
$pageUrl  = 'http://www.mono-log.org/blog';

do{
    // Get the html page
    $html = file_get_html( $pageUrl );

    // Load a page form the blog
    $mp3Library = array_merge(getAllMp3FromSinglePage( $html ) , $mp3Library);

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

echo json_encode( $mp3Library );


function getAllMp3FromSinglePage( $simpleDomPage )
{
    $html = $simpleDomPage;

    $pageResults = array();

    // Loop trough each mp3 link
        foreach( $html->find('.entry') as $songPost ) {

            // Save the url
            $songLink = $songPost->find('.entry-title a');
            $url = $songLink[0]->href;

            // Save the number
            $number = $songLink[0]->plaintext;

            // Get the date
            $songDate = $songPost->find('.entry-date');
            $date = $songDate[0]->plaintext;

            $pageResults[] = array( 'url' => $url, 'number' => $number, 'date' => $date  );

        }

    return $pageResults;
}

?>