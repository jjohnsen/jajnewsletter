<?php

require_once( 'extension/jajnewsletter/modules/newsletter/classes/jajnewsletteroperations.php' ); 

if ( !$isQuiet ) {
    $cli->output( "Delivering newsletter issues" );
}

JAJNewsletterOperations::doDeliveries();

?>
