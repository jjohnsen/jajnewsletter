<?php

include_once( 'kernel/classes/ezcontentobjecttreenode.php' );
include_once( 'kernel/classes/ezcontentfunctions.php' );

class JAJSubscriptionUser {
    function subscriptionUsersNodeID() {
        static $subscriptionUsersNodeID = null;
        if( $subscriptionUsersNodeID == null ) {
            $newsletterIni = eZINI::instance('jajnewsletter.ini');
            $subscriptionUsersNodeID = $newsletterIni->variable( 'ContentSettings', 'SubscriptionUsersNode' );
        }
        return $subscriptionUsersNodeID;
    }

    function subTreeByEmail( $email ) {
        $users = eZContentObjectTreeNode::subTree(
            array(
                'ClassFilterType' => 'include',
                'ClassFilterArray' => array( 'subscription_user' ),
                'AttributeFilter' => array(
                    array( 'subscription_user/email', '=', $email )
                ),
                'Limitation' => array()
            ), JAJSubscriptionUser::subscriptionUsersNodeID()
        );
        return $users;
    }

    function createSubscriptionUser($attributes) {
        $param  = array(
            'parent_node_id' => JAJSubscriptionUser::subscriptionUsersNodeID(),
            'class_identifier' => 'subscription_user',
            'creator_id' => ezUser::currentUserID(),
            'attributes' => array(
                'status' => 'Pending'
                //'subscriptions' => $subscriptions,
            )
        );

        $param['attributes'] = array_merge( $param['attributes'], $attributes );

        $object = eZContentFunctions::createAndPublishObject($param);
        $object->expireAllViewCache();
        return $object;
    }
    
    function subscriptionsObjectID($object) {
        $userDataMap = $object->DataMap();

        if( $userDataMap['subscriptions']->toString() )
            $userSubscriptions = explode( "-", $userDataMap['subscriptions']->toString() );
        else
            $userSubscriptions = array();

        return $userSubscriptions;
    }

    function updateSubscriptionUser($object, $attributes) {
        $modified = false;
        $userDataMap = $object->DataMap();
        $subscriptions = JAJSubscriptionUser::subscriptionsObjectID($object);
        $newSubscriptions = $subscriptions;

        if( $attributes['subscriptions_add'] ) {
            if( !is_array( $attributes['subscriptions_add'] ) )
                $newSubscriptions = array( $attributes['subscriptions_add'] );
            else
                $newSubscriptions = $attributes['subscriptions_add'];     
        }

        // TODO: Add transaction ?        
         
        $userSubDiff = array_diff( $newSubscriptions, $subscriptions );
        if( count( $userSubDiff ) ) {
            $newSubscriptions = array_unique( array_merge( $newSubscriptions, $subscriptions ) ); 
            
            $subscriptionAttribute = implode( "-", $newSubscriptions );
            $userDataMap['subscriptions']->fromString( $subscriptionAttribute );
            $userDataMap['subscriptions']->store();
            $modified = true;
        }
        
        if( isset( $attributes['name'] ) && 
            $attributes['name'] != $userDataMap['name']->toString() ) {
            $userDataMap['name']->fromString( $attributes['name'] );
            $userDataMap['name']->store();
            $modified = true;
        }
        
        if( isset( $attributes['status'] ) && 
            $attributes['status'] != $userDataMap['status']->toString() ) {
            $userDataMap['status']->fromString( $attributes['status'] );
            $userDataMap['status']->store();
            $modified = true;
        }

        if( $modified ) {
            $object->expireAllViewCache();
        }
        return $object;
    }
}
?>
