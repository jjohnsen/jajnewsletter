<?php
	require_once( 'kernel/common/template.php' );

  $newsletterIni = eZINI::instance('jajnewsletter.ini');
  $subscriptionListsNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionListsNode' );
  $subscriptionUsersNode = $newsletterIni->variable( 'ContentSettings', 'SubscriptionUsersNode' );

	$http = eZHTTPTool::instance();
	$offset = $Params['Offset'];
  if ( !$offset )
    $offset = 0;
  $limit = 25;
  
	$viewParameters = array( 'offset' => $offset, 'limit' => $limit );

  $tpl = templateInit();
  $tpl->setVariable( 'view_parameters', $viewParameters );
	$tpl->setVariable( 'subscription_lists_node', $subscriptionListsNode );
	$tpl->setVariable( 'subscription_users_node', $subscriptionUsersNode );

  $Result = array();
  $Result['content'] = $tpl->fetch( 'design:newsletter/lists_list.tpl' );
  $Result['path'] = array( array( 'url' => false,
                                  'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
?>
