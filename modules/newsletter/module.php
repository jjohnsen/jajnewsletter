<?php

$Module = array( 'name' => 'Newsletter',
                 'variable_params' => true );

$ViewList = array();

$ViewList['view'] = array(
    'script' => 'view.php',
    'functions' => array( 'administration' ),
    'params' => array ( 'remote_id' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$ViewList['newsletter_view'] = array(
    'script' => 'newsletter_view.php',
    'functions' => array( 'administration' ),
    'params' => array ( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$ViewList['newsletter_list'] = array(
    'script' => 'newsletter_list.php',
    'functions' => array( 'administration' ),
    'params' => array ( ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$ViewList['newsletter_preview'] = array(
    'script' => 'newsletter_preview.php',
    'functions' => array( 'administration' ),
    'params' => array ( 'NodeID' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$ViewList['newsletter_deliver'] = array(
    'script' => 'newsletter_deliver.php',
    'functions' => array( 'administration' ),
    'params' => array ( 'NodeID' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$ViewList['lists_list'] = array(
    'script' => 'lists_list.php',
    'functions' => array( 'administration' ),
    'params' => array ( ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$ViewList['lists_view'] = array(
    'script' => 'lists_view.php',
    'functions' => array( 'administration' ),
    'params' => array ( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset' ),
    'default_navigation_part' => 'jajnewsletternavigationpart' );

$FunctionList['administration'] = array( );
?>
