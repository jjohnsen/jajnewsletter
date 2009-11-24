<?php
    include_once( 'kernel/common/template.php' );
    include_once( 'lib/ezutils/classes/ezhttpfile.php' );
    include_once( 'kernel/classes/ezcontentfunctions.php' );
    include_once( 'kernel/classes/datatypes/ezuser/ezuser.php' );
    include_once( "lib/ezutils/classes/ezmail.php" );
 
    setlocale(LC_ALL, 'en_US.UTF-8');

    $newsletterIni = eZINI::instance('jajnewsletter.ini');
    $subscriptionListsNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionListsNode' );
    $subscriptionUsersNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionUsersNode' );

    $Module =& $Params['Module'];
    $http =& eZHTTPTool::instance();

    $delimiter = $http->hasPostVariable( 'CSVDelimiter' ) ? $http->variable( 'CSVDelimiter' ) : ',';
    $firstRowIsLabel = $http->hasPostVariable( 'FirstRowIsLabel' ) ? $http->variable( 'FirstRowIsLabel' ) : false;

    $rowSelection = array();

    if ( eZHTTPFile::canFetch( 'UploadCSVFile' ) ) {
        $binaryFile = eZHTTPFile::fetch( 'UploadCSVFile' );
        $handle = fopen( $binaryFile->attribute( 'filename' ), "r");
        $label = false;
 
        $data = array();     
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            if( $firstRowIsLabel && !$label ) {
                $label=true;
                continue;
            }
            
            $data[] = $row;
            $rowSelection[] = $rows;
            $rows++;
        }
        $http->setSessionVariable( 'CSVData', $data );
    }
    else if ( $http->hasSessionVariable( 'CSVData' ) )
    {
        if( $http->hasPostVariable( 'RowSelection' ) )
            $rowSelection = $http->variable('RowSelection');
        $data = $http->sessionVariable( 'CSVData' );
    }

    $addToSubscriptionLists = $http->hasPostVariable( 'ContentObjectID' ) ? 
        array( $http->variable( 'ContentObjectID' ) ) : array();;

    if( $http->hasPostVariable( 'SubscriptionListObjectID' ) ) {
        $addToSubscriptionLists = $http->variable('SubscriptionListObjectID');
    }

    $redirectIfDiscarded = $http->variable( 'RedirectIfDiscarded' );

    if ( $http->hasPostVariable( 'CancelButton' ) ) {
        if ( $http->hasSessionVariable( 'CSVData' ) )
            $http->removeSessionVariable( 'CSVData' );

        if ( $redirectIfDiscarded )
            return $Module->redirectTo( $redirectIfDiscarded );
        else
            return $Module->redirectToView( 'lists_list' );
    }
    do if ( $http->hasPostVariable( 'ImportButton' ) and count($data) > 0 ) {
        $warnings = array();
        if( count( $addToSubscriptionLists ) == 0 ) {
            $warnings[] = "Atleast one subscription list must be selected";
            break;
        }

        $subscriptions = implode( "-", $addToSubscriptionLists );

        $creator =& eZUser::currentUser();
        $creatorID =& $creator->attribute( 'contentobject_id' );

        $param_creation = array(
            'parent_node_id' => $subscriptionUsersNode,
            'class_identifier' => 'subscription_user',
            'creator_id' => $creatorID,
        );

        foreach( $data as $index => $row ) {
            if( !in_array( $index, $rowSelection ) )
                continue;
            if( strlen( $row[0] ) == 0 ) {
                $warnings[] = "Row " . ($index+1) . " skipped, missing name";
                continue;
            }
            if( !eZMail::validate( $row[1] ) ) {
                $warnings[] = "Row " . ($index+1) . " skipped, invalid email: '" . htmlspecialchars($row[1]) . "'";
                continue;
            }
            
            $param_creation['attributes'] = array(
                'name' => $row[0],
                'email' => $row[1],
                'subscriptions' => $subscriptions,
                'status' => 'Approved'
            );
            
            $object = eZContentFunctions::createAndPublishObject($param_creation);

            if( $object == false ) {
                $warnings[] = "Row " . ($index+1) . " skipped, failed to create subscriber with email: '" 
                    . htmlspecialchars($row[1]) . "'";
            }
        }    
        //if ( !eZMail::validate( $email ) )
        /*
        $creator =& eZUser::currentUser();
        $creatorID =& $creator->attribute( 'contentobject_id' );

        $attributes = array(
            'name' => 'Testing123',
            'email' => '123hikt.no',
            'subscriptions' => '282-2'
        );

        $param_creation = array(
            'parent_node_id' => $subscriptionUsersNode,
            'class_identifier' => 'subscription_user',
            'creator_id' => $creatorID,
            'attributes' => $attributes
        );
        
        $object = eZContentFunctions::createAndPublishObject($param_creation); 
        var_dump($object);
        
        $object = eZContentObject::fetch( 303 );
        $data_map = $object->DataMap();
        $person_list = $data_map[ 'subscriptions' ];
        */
        //var_dump($person_list->fromString("282-4"));
        /*$content = $person_list->content();
        $content['relation_list'][] = eZObjectRelationListType::appendObject( 282, 1, $person_list);

        var_dump( $content );
        $person_list->setContent( $content );
        $person_list->store();
        */
    } while ( false );

    $tpl =& templateInit();
    //$tpl->setVariable( 'subscriptionListNode', $subscriptionListNode );
    
    $tpl->setVariable( 'subscription_lists_node', $subscriptionListsNode );
    $tpl->setVariable( 'add_to_subscription_lists', $addToSubscriptionLists );
    $tpl->setVariable( 'CSVDelimiter', $delimiter );
    $tpl->setVariable( 'data', $data );
    $tpl->setVariable( 'row_selection', $rowSelection );
    $tpl->setVariable( 'warnings', $warnings );

    $Result = array();
    $Result['left_menu'] = "design:parts/jnewsletter/menu.tpl";
    $Result['content'] = $tpl->fetch( 'design:newsletter/users_import.tpl' );
    $Result['path'] = array( array( 'url' => false,
                                    'text' => ezi18n( 'newsletter', 'Newsletter' ) ) ); 
?>
