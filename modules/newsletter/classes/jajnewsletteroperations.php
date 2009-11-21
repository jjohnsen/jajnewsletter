<?php
    require_once('extension/jajnewsletter/modules/newsletter/classes/jajdelivery.php');
    require_once('extension/jajnewsletter/modules/newsletter/classes/jajnewsletterissue.php');
    require_once( 'lib/ezutils/classes/ezmail.php' );
    require_once( 'lib/ezutils/classes/ezmailtransport.php' );

	class JAJNewsletterOperations {
	    function prepareNewsletterIssue($newsletterIssueObject) {
	        $newsletterIni = eZINI::instance('jajnewsletter.ini');
	        $baseURI = $newsletterIni->variable( 'ServerSettings', 'BaseURI' );
	        $premailer = $newsletterIni->variable( 'ServerSettings', 'Premailer' );
	        $analyticsTracking = $newsletterIni->variable( 'NewsletterSettings', 'AnalyticsTracking' );
	        
	        $dataMap =& $newsletterIssueObject->dataMap();

                $mainNode = $newsletterIssueObject->mainNode(); 
	        $url = $baseURI . "/" . $mainNode->url();
                $cmd = $premailer . " " . escapeshellarg($url);
            
            if( $analyticsTracking ) {
                $campaign = urlencode( $dataMap['name']->content() );
                $querystring = "utm_source=newsletter&utm_medium=email&utm_campaign=" . $campaign;
                $cmd  = $premailer . " --querystring " . escapeshellarg($querystring);
                $cmd .= " --baseurl=' ' " . escapeshellarg($url);
            }
            
            exec( $cmd, $output, $return_var );
            
            //if($return_var != 0)
            //    return false;
             
            $messageBody = join($output);
            $messageBody = "body";    
            // TODO: Check for secret sign that everything was generated ok
            // TODO: Compress html (remove repeated whitespace)
            return $messageBody;
	    }
	    
	    function deliver( $subject, $messageBody, $fromName, $fromEmail, $replyTo, $recipientEmail) {
	        $mail = new eZMail();
            $mail->setSender( $fromEmail, $fromName );
            $mail->setReceiver( $recipientEmail );
            $mail->setReplyTo( $replyTo );

            $mail->setSubject( $subject );
            $mail->setBody( $messageBody );
            $mail->setContentType( "text/html" );
            
            $result = eZMailTransport::send( $mail );
            return $result;
	    }
	    
		function doDeliveries($quiet=false) {
		    $cli = eZCLI::instance();
		    $issueLimit = 5; // Number of issues the script will process each time
		    $deliveredCount = 0;    
		    $deliveryLimit = 200; // Number of deliveries the script will do each time. Should be multiples of 50
		    
		    $newsletterIni = eZINI::instance('jajnewsletter.ini');
		    $newsletterIssuesNodeID = $newsletterIni->variable( 'ContentSettings', 'NewsletterIssuesNode' );
			$subscriptionUsersNodeID = $newsletterIni->variable( 'ContentSettings', 'SubscriptionUsersNode' );
			$fromName = $newsletterIni->variable( 'NewsletterSettings', 'FromName' );
            $fromEmail = $newsletterIni->variable( 'NewsletterSettings', 'FromEmail' );
            $replyTo = $newsletterIni->variable( 'NewsletterSettings', 'ReplyTo' );
            
			// Get list of newsletter issues with status In Progress
			$newsletterIssues =& eZContentObjectTreeNode::subTreeByNodeID( 
              array(
                'ClassFilterType' => 'include',
                'ClassFilterArray' => array( 'newsletter_issue' ),
                'AttributeFilter' => array( 
                  array( 'newsletter_issue/status', '=', JAJ_NEWSLETTER_ISSUE_STATUS_PENDING )
                ),
                'Limit' => $issueLimit
                //'LoadDataMap' => false
              ), $newsletterIssuesNodeID
            );
            
            if(!$quiet)
                $cli->output( 'Newsletter issues awating delivery: ' . count($newsletterIssues) . ' (' . $issueLimit . ' max)');
            
            foreach( $newsletterIssues as $issue ) {                
                // Get newsletter and prepare
                $issueObject = $issue->object();
                $issueDatamap = $issueObject->dataMap();
                $newsletterSubject = $issueDatamap['subject']->content();
                
                if(!$quiet) {
                    $cli->output();
                    $cli->output( 'Delivering newsletter \'' . $newsletterSubject 
                        . '\' (Object id: ' . $issueObject->ID . ')' );
                }
                
                $newsletterBody  = JAJNewsletterOperations::prepareNewsletterIssue($issueObject);
                
                if($newsletterBody == false) {
                    if(!$quiet)
                        $cli->notice( 'Failed to generate newsletter \'' . $newsletterSubject 
                            . '\' (Object id: ' . $issueObject->ID . ')' );
                    continue;
                }
                
                // Go though users in delivery que in batch and deliver newsletter
                $userOffset = 0;
                $userLimit = 50;
                while(true) {    
                    // Get users in delivery que for current newsletteer
                    // TODO: Should only get items where jajdelivery.tstamp > 1 hour or something
                    $userNodes =& eZContentObjectTreeNode::subTreeByNodeID( 
                        array(
                            'ClassFilterType' => 'include',
                            'ClassFilterArray' => array( 'subscription_user' ),
                            'ExtendedAttributeFilter' => array(
                                id => 'jajdeliveryfilter',
                                params => array(
                                    'newsletter_object_id' => $issueObject->ID,
                                    'status' => array( JAJ_NEWSLETTER_DELIVERY_STATUS_PENDING )
                                )
                            ),
                            'Limit' => $userLimit,
                            'Offset' => $userOffset,
                            //'LoadDataMap' => false
                        ), $subscriptionUsersNodeID
                    );
                    
                    $userCount = count($userNodes);
                    
                    if(!$quiet)
                        $cli->output( '     Users found for batch delivery: ' 
                            . $userCount . ' (' . $userLimit . ' max in batch)');
                    
                    foreach( $userNodes as $userNode ) {
                        $userObject = $userNode->object();
                        $userDatamap = $userObject->dataMap();
                        $userEmail = $userDatamap['email']->content();
                        
                        if(!$quiet)
                            $cli->output( '     Delivereing to: ' . $userEmail, false );
                       
                        
                        $deliveryResult = JAJNewsletterOperations::deliver( 
                            $newsletterSubject, $newsletterBody, 
                            $fromName, $fromEmail, $replyTo, $userEmail
                        );
                        
                        $delivery = JAJDelivery::fetchDelivery( $issueObject->ID, $userObject->ID );
                        $delivery->setAttribute( 'tstamp', time() );
                        
                        if( $deliveryResult ) {
                            if(!$quiet)
                                $cli->output( ' => OK');
                            $delivery->setAttribute( 'status', JAJ_NEWSLETTER_DELIVERY_STATUS_SENT ); 
                        }
                        else {  
                            $tries = $delivery->attribute( 'tries' ) + 1;
                            $delivery->setAttribute( 'tries', $tries );
                            
                            if($tries >= 3)
                                $delivery->setAttribute( 'status', JAJ_NEWSLETTER_DELIVERY_STATUS_FAILED );
                            
                            if(!$quiet)
                                $cli->output( ' => FAILED, tries: ' . $tries );
                        }
                        $delivery->sync();
                        $deliveredCount++;
                    }
                    
                    eZContentObject::clearCache();

                    if( $userCount < $userLimit || $deliveredCount >= $deliveryLimit)
                        break;
                    else
                        $userOffset += $userLimit;
                }
                
                if( $deliveredCount >= $deliveryLimit ) {
                    if(!$quiet)
                        $cli->output( 'Reached delivery limit for script (' . $deliveredCount . '/' . $deliveryLimit . ')' );
                    break;
                }
                
                // Change status to archived if delivery que for newsletter is empty
                if( JAJDelivery::emptyDeliveryQue( $issueObject->ID ) ) {
                    if(!$quiet)
                        $cli->output( 'Delivery que for newsletter empty, changing status to archived' );
                    
                    $status = $issueDatamap["status"];
                    $status->setAttribute( 'data_text', JAJ_NEWSLETTER_ISSUE_STATUS_ARCHIVED );
                    $status->sync();
                }
            }
		}
	}
?>
