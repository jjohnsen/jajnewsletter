<?php
    //select ezcontentobject.*, jajdelivery.status as delivery_status from ezcontentobject,jajdelivery where ezcontentobject.contentclass_id=56 AND subscription_user_id=ezcontentobject.id and newsletter_issue_id=99 and jajdelivery.status IN (0);
    class JAJDeliveryFilter {
        function DeliverySqlParts( $params ) {
            if( !is_array( $params ) || !is_numeric( $params["newsletter_object_id"] ) || !is_array( $params['status'] ) )
                return array();
            
            $id = mysql_real_escape_string( $params["newsletter_object_id"] );
            $status = mysql_real_escape_string( implode(', ', $params['status'] ) );
            
            $tables = ', jajdelivery';
            $columns = ', jajdelivery.status as delivery_status';
            $joins  = ' jajdelivery.subscription_user_id=ezcontentobject.id AND';
            $joins .= ' jajdelivery.newsletter_issue_id=' . $id . ' AND';
            $joins .= ' jajdelivery.status IN ( ' . $status . ' ) AND';

            return array( 'tables' => $tables, 'columns' => $columns, 'joins' => $joins );
        }
    }
?>
