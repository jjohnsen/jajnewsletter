<?php
    require_once('extension/jajnewsletter/modules/newsletter/classes/jajdelivery.php');
    require_once('extension/jajnewsletter/modules/newsletter/classes/jajnewsletterissue.php');
    require_once( 'kernel/classes/ezcontentobjecttreenode.php' );
    require_once( 'extension/jajnewsletter/lib/php4/PHPMailer_v2.0.4/class.phpmailer.php' );
	
    class JAJNewsletterOperations {
        function prepareNewsletterIssue($newsletterIssueObject) {
	        $newsletterIni = eZINI::instance('jajnewsletter.ini');
	        $baseURI = $newsletterIni->variable( 'ServerSettings', 'BaseURI' );
	        $premailer = $newsletterIni->variable( 'ServerSettings', 'Premailer' );
	        $analyticsTracking = $newsletterIni->variable( 'NewsletterSettings', 'AnalyticsTracking' );
	        
	        $dataMap =& $newsletterIssueObject->dataMap();
                                
          	$mainNode = $newsletterIssueObject->mainNode(); 
	        $url = $baseURI . "/" . $mainNode->url();
                
                $cmd  = extension_path("jajnewsletter") . "/" . $premailer;
                $cmd .= " " . escapeshellarg($url); 
            
                if( $analyticsTracking ) {
                    $campaign = urlencode( $dataMap['name']->content() );
                    $querystring = "utm_source=newsletter&utm_medium=email&utm_campaign=" . $campaign;
                    $cmd .= " --querystring " . escapeshellarg($querystring);
                }
            //echo $cmd;
            exec( $cmd, $output, $return_var );
            if($return_var != 0)
                return false;
             
            $messageBody = join("\n", $output);
            
            $cmd .= " --plaintext";

            exec( $cmd, $outputPlain, $return_var );
            if($return_var != 0)
                return false;

            $plainBody = join("\n", $outputPlain);
  
            // TODO: Check for secret sign that everything was generated ok
            // TODO: Compress html (remove repeated whitespace)
            return array('html' => $messageBody, 'plain' => $plainBody);
	}
	
        function deliver( $subject, $htmlBody, $plainBody, $fromName, $fromEmail, $replyTo, $recipientEmail) {
            $ini =& eZINI::instance();
            
            $iniI18N =& eZINI::instance( "i18n.ini" );
            $charSet = $iniI18N->variable( 'CharacterSettings', 'Charset' );
            if( $charSet != "iso-8859-1") {
              $subject = iconv( $charSet, 'ISO-8859-1//TRANSLIT//IGNORE', $subject);
              $htmlBody = iconv( $charSet, 'ISO-8859-1//TRANSLIT//IGNORE', $htmlBody);
              $plainBody = iconv( $charSet, 'ISO-8859-1//TRANSLIT//IGNORE', $plainBody);
            }
            
            $mail = new PHPMailer(); 
            $mail->IsSMTP();
            //$mail->CharSet = "UTF-8";
            //$mail->Encoding = "base64";
            $mail->Host = $ini->variable( 'MailSettings', 'TransportServer' );
            
            $mail->From = $fromEmail;
            $mail->FromName = $fromName;
            $mail->AddReplyTo($replyTo, $fromName);
            
            $mail->Subject = $subject;
            $mail->MsgHTML( $htmlBody );
            $mail->AltBody = $plainBody;
            $mail->AddAddress($recipientEmail);
 
            return $mail->Send();
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
        //$newsletterIssues =& eZContentObjectTreeNode::subTreeByNodeID( 
                        
        $newsletterIssues =& eZContentObjectTreeNode::subTree(
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
            $userLimit = 50;
            while(true) {    
                // Get users in delivery que for current newsletteer
                // TODO: Should only get items where jajdelivery.tstamp > 1 hour or something
                //$userNodes =& eZContentObjectTreeNode::subTreeByNodeID(
                $userNodes =& eZContentObjectTreeNode::subTree(
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
                        'Limitation' => array(),
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

                    $htmlNewsletter = $newsletterBody['html'];
                    $plainNewsletter = $newsletterBody['plain'];
                    
                    $htmlNewsletter = str_replace( "__remote_id", $userObject->remoteID(), $htmlNewsletter );
                    $htmlNewsletter = str_replace( "__object_id", $userObject->ID, $htmlNewsletter );
                    $plainNewsletter = str_replace( "__remote_id", $userObject->remoteID(), $plainNewsletter );
                    $plainNewsletter = str_replace( "__object_id", $userObject->ID, $plainNewsletter );
                     
                    $newsletterDeliveryResult = JAJNewsletterOperations::deliver( 
                        $newsletterSubject, $htmlNewsletter,
                        $plainNewsletter, 
                        $fromName, $fromEmail, $replyTo, $userEmail
                    );
                                 
                    $deliveryResult = JAJDelivery::fetchDelivery( $issueObject->ID, $userObject->ID );
                    $delivery = $deliveryResult['result'];
                    $delivery->setAttribute( 'tstamp', time() );
                        
                    if( $newsletterDeliveryResult ) {
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

                if( $userCount < $userLimit || $deliveredCount >= $deliveryLimit ) {
                    break;
                }
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
