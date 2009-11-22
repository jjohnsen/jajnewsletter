<?php
/**
 * Enhanced object relation filter
 * Parameters format: array( array( 'class/attribute', object_id) )
 *                           array( 'attribute_id, array(object_id1, object_id2) )
 * With the current implementation, the attribute has to have relation to EVERY object
 * it is listed for. Implementation of OR filtering is possible though.
 *
 * @author Bertrand Dunogier <bd@ez.no>
 * @copyright 2005
 */
class EORExtendedFilter
{

  function CreateSqlParts( $params )
  {
    $db =& eZDB::instance();

    $tables = array();
    $joins  = array();

    // foreach filtered attribute, we add a join the relation table and filter
    // on the attribute ID + object ID
    foreach( $params as $param )
    {
        if ( !is_array( $param ) )
            continue;
        
	if ( !is_numeric( $param[0] ) )
        {
            $classAttributeId = eZContentObjectTreeNode::classAttributeIDByIdentifier( $param[0] );
        }
        else
        {
            $classAttributeId = $param[0];
        }

        // multiple objects ids
        if ( is_array($param[1]) )
        {
	    	// Patch from Grégory BECUE
            // http://ez.no/developer/forum/developer/fetch_and_filter_on_object_relations/re_fetch_and_filter_on_object_relations__2
            // Handle 'or' parameters
      		if($param[2] == 'or') 
  			{
    			$cpt = 0;
    			$chaineCritere = "(";
    
    			foreach( $param[1] as $objectId )
    			{
      				if ( is_numeric( $objectId ) )
      				{
        				if($cpt == 0)
        				{       
			          		$tableName = 'eor_link_' . $objectId;
          					$tables[] = 'ezcontentobject_link ' . $tableName;
          					$joins[] = $tableName . '.from_contentobject_id = ezcontentobject.id';
          					$joins[] = $tableName . '.from_contentobject_version = ezcontentobject.current_version';
          					$joins[] = $tableName . '.contentclassattribute_id = ' . $classAttributeId;
          					$chaineCritere .= $tableName . '.to_contentobject_id = ' . $objectId;
        				}
        				else
        				{
          					$chaineCritere .= ' or '.$tableName . '.to_contentobject_id = ' . $objectId;    
        				}       
      				}
      				$cpt++;
    			}              
    			$joins[] = $chaineCritere.")";
  			}
			// Handle 'and'
            else
	    	{ 
				foreach( $param[1] as $objectId )
				{
                	if ( is_numeric( $objectId ) )
                	{
                    	$tableName = 'eor_link_' . $objectId;
                    	$tables[] = 'ezcontentobject_link ' . $tableName;

		               	$joins[]  = $tableName . '.from_contentobject_id = ezcontentobject.id';
                		$joins[]  = $tableName . '.from_contentobject_version = ezcontentobject.current_version';
                    	$joins[]  = $tableName . '.contentclassattribute_id = ' . $classAttributeId;
                    	$joins[]  = $tableName . '.to_contentobject_id = ' . $objectId;
                	}
            	}
			}

        }
        // single object id
        else
        {
            $objectId = $param[1];

            $tableName = 'eor_link_' . $objectId;
            $tables[] = 'ezcontentobject_link ' . $tableName;

            $joins[]  = $tableName . '.from_contentobject_id = ezcontentobject.id';
            $joins[]  = $tableName . '.from_contentobject_version = ezcontentobject.current_version';
            $joins[]  = $tableName . '.contentclassattribute_id = ' . $classAttributeId;
            $joins[]  = $tableName . '.to_contentobject_id = ' . $objectId;
        }
    }

    if ( !count( $tables ) or !count( $joins ) )
    {
      $tables = $joins = '';
    }
    else
    {
      $tables = "\n, "    . implode( "\n, ", $tables );
      $joins =  implode( " AND\n ", $joins ) . " AND\n ";
    }

    return array( 'tables' => $tables, 'joins' => $joins );
  }
}
