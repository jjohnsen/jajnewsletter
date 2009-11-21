<?php
	require_once( 'kernel/common/template.php' );

  	$newsletterIni = eZINI::instance('jajnewsletter.ini');
  	$newsletterIssuesNode = $newsletterIni->variable( 'ContentSettings', 'NewsletterIssuesNode' );

	$http = eZHTTPTool::instance();
	
	$offset = $Params['Offset'];

    	if ( !$offset )
        	$offset = 0;
    	$limit = 25;

	$viewParameters = array( 'offset' => $offset, 'limit' => $limit );

    	$tpl = templateInit();
    	$tpl->setVariable( 'view_parameters', $viewParameters );
	$tpl->setVariable( 'newsletter_issues_node', $newsletterIssuesNode );

    	$Result = array();
    	$Result['content'] = $tpl->fetch( 'design:newsletter/newsletter_list.tpl' );
    	$Result['path'] = array( array( 'url' => false,
                                    	'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
                                    
?>
