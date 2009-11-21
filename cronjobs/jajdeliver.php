<?php

if ( !$isQuiet ) {
    $cli->output( "Delivering newsletter issues" );
}

JAJNewsletterOperations::doDeliveries();

?>
