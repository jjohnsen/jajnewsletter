<?php
    require_once( 'kernel/common/template.php' );
    include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
    include_once( "lib/ezutils/classes/ezmail.php" );
    include_once( 'extension/jajnewsletter/modules/newsletter/classes/jajsubscriptionuser.php' );

    $http = eZHTTPTool::instance();
    $Module =& $Params['Module'];

    $remoteID = $Params['RemoteID'];
    $node = eZContentObjectTreeNode::fetchByRemoteID( $remoteID );
    
    if ( !$node )
        return $Module->handleError( EZ_ERROR_KERNEL_NOT_AVAILABLE, 'kernel' );

    // TODO CHECK FOR TRASH
    // TODO CHECK FOR READ
    
    $success = array();
    $error = array();
    $email = "";
    $name = "";

    do if ( $http->hasPostVariable( 'SignupButton' ) ) { 
        
        $email = trim( $http->variable( 'email' ) );
        $name = trim( $http->variable( 'name' ) );

        if( !eZMail::validate( $email ) )
            $error['email'] = true;

        if( strlen($name) == 0 )
            $error['name'] = true;

        if( !empty( $error ) ) 
            continue;
        
        // Check if user allready exists
        $users = JAJSubscriptionUser::subTreeByEmail( $email );
        
        // If we have more than one user with the same email we have a fatal
        // problem
        if( count( $users ) > 1 ) {
            $error['fatal'] = true;
            continue;
        }
        
        if( count( $users ) == 0 ) {
            $attributes = array(
                'name' => $name,
                'email' => $email,
                'subscriptions' => $node->ContentObjectID
            );
            
            $user = JAJSubscriptionUser::createSubscriptionUser($attributes);
            //TODO: CHECK IF USER WAS CREATED
            $success['created'] = true;
            continue;
        }
        
        if( count( $users ) == 1 ) {
            $user = $users[0];
            $userObject = $user->object();
            
            $userDataMap = $userObject->DataMap();
            $userStatus = $userDataMap['status']->toString();
            
            if($userStatus == "Removed by admin") {
                $error['removed_by_admin'] = true;
                continue;
            }
            
            if( !in_array( $userStatus, array(  'Approved', 'Confirmed' ) ) ) {
                $userStatus = 'Pending';
            }

            $attributes = array(
                'name' => $name,
                'subscriptions_add' => $node->ContentObjectID,
                'status' => $userStatus
            );

            $userObject = JAJSubscriptionUser::updateSubscriptionUser($userObject, $attributes);

            $success['created'] = true;
            continue; 
        } 

    } while ( false );

    $tpl = templateInit();
    $tpl->setVariable( 'node', $node );
    $tpl->setVariable( 'module', $Module );
    $tpl->setVariable( 'error', $error );
    $tpl->setVariable( 'success', $success );
    $tpl->setVariable( 'email', $email );
    $tpl->setVariable( 'name', $name );

    $Result = array();
//    $Result['left_menu'] = "design:parts/jnewsletter/menu.tpl";
    $Result['content'] = $tpl->fetch( 'design:newsletter/sign_up.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
?>
