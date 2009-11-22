<?php
    define( 'JAJ_USER_STATUS_PENDING', 0 );
    define( 'JAJ_USER_STATUS_CONFIRMED', 1 );
    define( 'JAJ_USER_STATUS_APPROVED', 2 );
    define( 'JAJ_USER_STATUS_REMOVED_SELF', 3 );
    define( 'JAJ_USER_STATUS_REMOVED_BY_ADMIN', 4 );
    define( 'JAJ_USER_STATUS_INALID', 5 );

  require_once( 'errors.php' );
  require_once( 'kernel/common/template.php' );
  require_once( 'extension/jajnewsletter/modules/newsletter/classes/jajdelivery.php' );

  $newsletterIni = eZINI::instance('jajnewsletter.ini');
  $subscriptionUsersNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionUsersNode' );
  
  $nodeID = $Params['NodeID'];
  $node =& eZContentObjectTreeNode::fetch( $NodeID );
  $userNode =& eZContentObjectTreeNode::fetch( $subscriptionUsersNode );
  
  if ( !$node || !$userNode )
      return $Module->handleError( KERNEL_NOT_AVAILABLE, 'kernel' );
      
  $object =& $node->object();
  $userObject =& $userNode->object();
  
  if ( !$object->canRead() || !$userObject->canRead() )
      return $Module->handleError( 
        KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $object->accessList( 'read' ) ) 
      );
  
  $dataMap =& $object->dataMap();
  
  // Build array of object_ids for the lists we should deliver to
  $relationList = $dataMap['subscription_lists']->content();
  $relationList = $relationList['relation_list'];
  $distributionLists = array();
  
  if( count( $relationList ) == 0 )
    return $Module->handleError( JAJ_ERROR_NEWSLETTER_EMPTY_SUBSCRIPTION_LISTS, 'newsletter' );
  
  foreach($relationList as $relation) {
    array_push( $distributionLists, $relation['contentobject_id'] );
  }
  
  // Get list of users the newsletter allready has been delivered to
  $rows = eZPersistentObject::fetchObjectList( 
    JAJDelivery::definition(),
    array('subscription_user_id'),
    array('newsletter_issue_id' => $object->ID),
    array('subscription_user_id' => 'asc'),
    null,
    false
  );
  
  // Extract user_ids
  $delivered = array();
  foreach($rows as $row)
     array_push($delivered, $row['subscription_user_id']);

  // Status for users we should send newsletter to
  $valid_status = array(JAJ_USER_STATUS_PENDING, JAJ_USER_STATUS_CONFIRMED, JAJ_USER_STATUS_APPROVED);
  $limit = 50;
  $offset = 0;
  
  // Loop through users, 50 at a time to avoid excessive memory usage
  $recipient_count = 0;
  while(true) {
    //$users =& eZContentObjectTreeNode::subTreeByNodeID( 
    $users =& eZContentObjectTreeNode::subTree(
      array(
        'ClassFilterType' => 'include',
        'ClassFilterArray' => array( 'subscription_user' ),
        'AttributeFilter' => array( 
          array( 'subscription_user/status', 'in', $valid_status )
        ),
        'ExtendedAttributeFilter' => array(
          id => 'eorfilter',
          params => array(array('subscription_user/subscriptions', $distributionLists, 'or'))
        ),     
        'Limit' => $limit,
        'Offset' => $offset,
        'LoadDataMap' => false
      ), $subscriptionUsersNode
    );
    
    foreach( $users as $user ) {
        if( in_array( $user->ContentObjectID, $delivered ))
            continue;
        
        $row = array(  'newsletter_issue_id' => $object->ID, 'subscription_user_id' =>  $user->ContentObjectID );
        $delivery = new JAJDelivery($row);
        $delivery->store();
        
        $recipient_count++;
    }
    
    eZContentObject::clearCache();
    
    if( count($users) < $limit )
      break;
    else
      $offset += $limit;
  }
  
  // Update Newsletter issue status if we found new recipients
  if( $recipient_count > 0) {
      $status = $dataMap["status"];
      $status->setAttribute( 'data_text', "1" );
      $status->sync();
  }
  
  $tpl = templateInit();
  $tpl->setVariable( 'recipient_count', $recipient_count );
  $tpl->setVariable( 'newsletter_name', $dataMap['name']->content() );
  
	
  $Result = array();
  $Result['content'] = $tpl->fetch( 'design:newsletter/newsletter_deliver.tpl' );
  $Result['path'] = array( array( 'url' => false,
                                  'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
?>
