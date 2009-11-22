<?php
  require("errors.php");
  require_once( 'kernel/common/template.php' );
  
  $newsletterIni = eZINI::instance('jajnewsletter.ini');
  $subscriptionListsNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionListsNode' );
  $subscriptionUsersNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionUsersNode' );
  
  $nodeID = $Params['NodeID'];
  $node =& eZContentObjectTreeNode::fetch( $NodeID );

  if ( !$node )
      return $Module->handleError( KERNEL_NOT_AVAILABLE, 'kernel' );
      
  $object =& $node->object();
  if ( !$object->canRead() )
      return $Module->handleError( 
        KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $object->accessList( 'read' ) ) 
      );
  
  $offset = $Params['Offset'];
  if ( !$offset )
    $offset = 0;
  $limit = 25;

  $viewParameters = array( 'offset' => $offset, 'limit' => $limit );
  	
  $tpl = templateInit();
  $tpl->setVariable( 'node_id', $NodeID );
  $tpl->setVariable( 'view_parameters', $viewParameters );
  $tpl->setVariable( 'subscription_lists_node', $subscriptionListsNode );
  $tpl->setVariable( 'subscription_users_node', $subscriptionUsersNode );
  	
  $Result = array();
  $Result['content'] = $tpl->fetch( 'design:newsletter/lists_view.tpl' );
  $Result['path'] = array( array( 'url' => false,
                                  'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
?>
