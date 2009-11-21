<?php
class jajNewsletterHandler extends eZContentObjectEditHandler
{
    function fetchInput( &$http, &$module, &$class, &$object, &$version, &$contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage )
    {
	if( $module->isCurrentAction( 'Store' ) ) {
		echo $editVersion;
		/*
		echo "ok";
		var_dump($object->mainNode());
		var_dump($contentObjectAttributes);
		var_dump($object);
		*/

		$tpl = templateInit();
		$node = $object->mainNode();
		$contentObject = $object;
		$LanguageCode = $editLanguage;

$contentObject->setAttribute( 'current_version', "27" );

          	$pathIdentificationString = "";
          	$ObjectID = 99;
          	$parentNodeID = 2;
          	$virtualNodeID = null;
          	$pathString = "";
          	$depth = 2;
          	$objectName = "OMG";
          
          $node = new eZContentObjectTreeNode();
          $node->setAttribute( 'contentobject_version', 27 );
          $node->setAttribute( 'path_identification_string', $pathIdentificationString );
          $node->setAttribute( 'contentobject_id', $ObjectID );
          $node->setAttribute( 'parent_node_id', $parentNodeID );
          $node->setAttribute( 'main_node_id', $virtualNodeID );
          $node->setAttribute( 'path_string', $pathString );
          $node->setAttribute( 'depth', $depth );
          $node->setAttribute( 'node_id', $virtualNodeID );
          //$node->setAttribute( 'sort_field', $class->attribute( 'sort_field' ) );
          //$node->setAttribute( 'sort_order', $class->attribute( 'sort_order' ) );
          $node->setName( $objectName );
	 $node->setContentObject( $contentObject );	
		eZContentLanguage::expireCache();
/*
	$node = $object->mainNode();
	$node->setAttribute( 'contentobject_version', 27 );
	$node->setContentObject( $contentObject );
*/

var_dump($contentObject->ContentObjectAttributes);	
/*
$access = $GLOBALS['eZCurrentAccess'];
$access['name'] = $siteAccess;

if ( $access['type'] == EZ_ACCESS_TYPE_URI )
{
    eZSys::clearAccessPath();
}
changeAccess( $access );
*/
		$Result = eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject, $LanguageCode, 'full', 0);
	}
    }
 
    static function storeActionList()
    {
    }
 
    function publish( $contentObjectID, $contentObjectVersion )
    {
 
    }
}
?>
