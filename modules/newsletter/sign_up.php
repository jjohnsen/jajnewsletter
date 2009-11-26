<?php
    require_once( 'kernel/common/template.php' );

    $http = eZHTTPTool::instance();
    $Module =& $Params['Module'];

    $remoteID = $Params['RemoteID'];
    $node = eZContentObjectTreeNode::fetchByRemoteID( $remoteID );

    if ( !$node )
        return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );

    // TODO CHECK FOR TRASH
    // TODO CHECK FOR READ

    $error = array();
    $error['name'] = true;
    $error['email'] = true;

    $tpl = templateInit();
    $tpl->setVariable( 'node', $node );
    $tpl->setVariable( 'module', $Module );
    $tpl->setVariable( 'error', $error );

    $Result = array();
//    $Result['left_menu'] = "design:parts/jnewsletter/menu.tpl";
    $Result['content'] = $tpl->fetch( 'design:newsletter/sign_up.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
?>
