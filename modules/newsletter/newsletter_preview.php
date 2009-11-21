<?php
  require("errors.php");
  require_once( 'kernel/common/template.php' );
  
  $newsletterIni = eZINI::instance('jajnewsletter.ini');
  $fromName = $newsletterIni->variable( 'NewsletterSettings', 'FromName' );
  $fromEmail = $newsletterIni->variable( 'NewsletterSettings', 'FromEmail' );
  $replyTo = $newsletterIni->variable( 'NewsletterSettings', 'ReplyTo' );
  
  $nodeID = $Params['NodeID'];
  $node =& eZContentObjectTreeNode::fetch( $NodeID );

  if ( !$node )
      return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
      
  $object =& $node->object();
  if ( !$object->canRead() )
      return $Module->handleError( 
        eZError::KERNEL_ACCESS_DENIED, 'kernel', array( 'AccessList' => $object->accessList( 'read' ) ) 
      );
  
  $dataMap =& $object->dataMap();

  $newsletterSubject = "[PREVIEW] " . $dataMap['subject']->content();
  $previewEmail = $dataMap['preview_email']->content();
  
  $newsletterBody  = JAJNewsletterOperations::prepareNewsletterIssue($object);
  
  if($newsletterBody == false)
      return $Module->handleError( JAJ_ERROR_NEWSLETTER_CONVERT_FAIL, 'newsletter' );
  
  $deliveryResult = JAJNewsletterOperations::deliver( 
      $newsletterSubject, $newsletterBody, 
      $fromName, $fromEmail, $replyTo, $previewEmail
  );
  
  if( $deliveryResult ) {
      $tpl = templateInit();
      $tpl->setVariable( 'preview_email', $previewEmail );

      $Result = array();

      $Result['content'] = $tpl->fetch( 'design:newsletter/newsletter_preview.tpl' );
      $Result['path'] = array( array( 'url' => false,
          'text' => ezi18n( 'newsletter', 'Newsletter' ) ) );
  } else {
      return $Module->handleError( JAJ_ERROR_NEWSLETTER_SEND_FAIL, 'newsletter' );
  }
?>
