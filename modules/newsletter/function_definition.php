<?php
	$FunctionList = array();
	
	$FunctionList['delivery_count'] = array( 
	    'name' => 'delivery_count',
            'call_method' => array( 
                'include_file' => 'extension/jajnewsletter/modules/newsletter/classes/jajdelivery.php',
                'class' => 'JAJDelivery',
                'method' => 'fetchDeliveryCount' 
            ),
            'parameter_type' => 'standard',
            'parameters' => array ( 
                array ( 
                    'name' => 'newsletter_issue_object_id',
                    'type' => 'integer',
                    'required' => true 
                ),
                array ( 
                    'name' => 'status',
                    'type' => 'array',
                    'required' => false 
                )
            ) 
        );
        
        $FunctionList['delivery_status'] = array(
            'name' => 'delivery_status',
            'call_method' => array(
                'include_file' => 'extension/jajnewsletter/modules/newsletter/classes/jajdelivery.php',
                'class' => 'JAJDelivery',
                'method' => 'fetchDelivery'
            ),
            'parameter_type' => 'standard',
            'parameters' => array (
                array (
                    'name' => 'newsletter_issue_object_id',
                    'type' => 'integer',        
                    'required' => true
                ),
                array (
                    'name' => 'subscription_user_object_id',
                    'type' => 'integer',
                    'required' => true
                )
            )
        );
?>
