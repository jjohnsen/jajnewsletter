<?php

define( 'JAJ_NEWSLETTER_DELIVERY_STATUS_PENDING',   0 );
define( 'JAJ_NEWSLETTER_DELIVERY_STATUS_SENT',      1 );
define( 'JAJ_NEWSLETTER_DELIVERY_STATUS_FAILED',    2 );
define( 'JAJ_NEWSLETTER_DELIVERY_STATUS_CANCELLED', 3 );
define( 'JAJ_NEWSLETTER_DELIVERY_STATUS_INVALID',   4 );

include_once( "kernel/classes/ezpersistentobject.php" );

class JAJDelivery extends eZPersistentObject
{
    function JAJDelivery( $row = array() )
    {
        $this->eZPersistentObject( $row );
    }

    # Static not supported on PHP4    
    function definition() 
    {
        return array(
            "fields" => array( 
                "id" => array(
                    "name" => "ID",
                    "datatype" => "integer",
                    "default" => 0,
                    "required" => true 
                ),
                "newsletter_issue_id" => array(
                    "name" => "NewsletterIssueID",
                    "datatype" => "integer",
                    "default" => 0,
                    "required" => true 
                ),
                "subscription_user_id" => array(
                    "name" => "SubscriptionUserID",
                    "datatype" => "integer",
                    "default" => 0,
                    "required" => true 
                ),
                "status" => array(
                    "name" => "Status",
                    "datatype" => "integer",
                    "default" => 0,
                    "required" => true 
                ),
                "tries" => array(
                    "name" => "Tries",
                    "datatype" => "integer",
                    "default" => 0,
                    "required" => false
                ),
                "tstamp" => array(
                    "name" => "TStamp",
                    "datatype" => "integer",
                    "default" => 0,
                    "required" => false 
                )
            ),
            "keys" => array( "id" ),
            "increment_key" => "id",
            "class_name" => "JAJDelivery",
            "name" => "jajdelivery"
        );
    }
    
    /*!
        \static
        Fetch delivery que item
        \return integer count
    */    
    function fetchDelivery( $newsletterIssueObjectID, $subscriptionUserObjectID ) {
        $result = eZPersistentObject::fetchObject(
            JAJDelivery::definition(), 
            null,
            array( 
                'newsletter_issue_id' => $newsletterIssueObjectID,
                'subscription_user_id' => $subscriptionUserObjectID
            )
            ,
            true
        );
        
        return $result;
    }
    
    /*!
        \static
        Fetch usercount for newsletter issue, optional by status
        \return integer count
    */
    function fetchDeliveryCount( $newsletterIssueObjectID, $status = null )
    {
        $conds = array('newsletter_issue_id' => $newsletterIssueObjectID);
        
        if( is_string( $status ) || is_int( $status ) ) 
            $conds['status'] = $status; 
        elseif( is_array( $status ) )
            $conds['status'] = array( $status );
        
         
        //$count = eZPersistentObject::count( JAJDelivery::definition(), $conds );
        $def = JAJDelivery::definition();
        $field = '*';
        $customFields = array( array( 'operation' => 'COUNT( ' . $field . ' )', 'name' => 'row_count' ) );
        $rows = eZPersistentObject::fetchObjectList( $def, array(), $conds, null, null, false, false, $customFields );
        return array( 'result' => $rows[0]['row_count'] );
    }

    /*!
        \static
        Checks if the delivery que for newsletter issue is empty
        \return boolean
    */    
    function emptyDeliveryQue( $newsletterIssueObjectID )
    {
        $result = JAJDelivery::fetchDeliveryCount( 
            $newsletterIssueObjectID, 
            array( JAJ_NEWSLETTER_DELIVERY_STATUS_PENDING ) 
        );
        
        return ($result["result"] == 0);
    }
}
?>
