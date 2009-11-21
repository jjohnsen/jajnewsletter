<?php
    require_once( 'kernel/common/template.php' );

    $http = eZHTTPTool::instance();
    
    $tpl = templateInit();
    
    $remote_id = "07c1b1f7979bfbc0c2829a4caa04d0bf";
    $newsletterIssueObject = eZContentObject::fetchByRemoteID($remote_id);
    
    if ( !$newsletterIssueObject )
        return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
        
    if ( !$newsletterIssueObject->canRead() )
        return $Module->handleError( 
            eZError::KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $object->accessList( 'read' ) ) 
        );
        
    //$node = $newsletterIssueObject->mainNode();
    
    $NodeID = $newsletterIssueObject->mainNodeID();
    
    $Module = $Params['Module'];
    $ViewMode = 'full';
    
    $localVars = array( "cacheFileArray", "NodeID",   "Module", "tpl",
                        "LanguageCode",   "ViewMode", "Offset", "ini",
                        "cacheFileArray", "viewParameters",  "collectionAttributes",
                        "validation" );
                        
    $args = compact( $localVars );
    $data = eZNodeviewfunctions::contentViewGenerate( false, $args ); // the false parameter will disable generation of the 'binarydata' entry
    return $data['content']; // Return the $Result array
    
?>