<?php

/*!
  \class   ezjajpreviewnewslettertype ezjajpreviewnewslettertype.php
  \ingroup eZDatatype
  \brief   Håndterer datatypen jajpreviewnewsletter. Ved å bruke jajpreviewnewsletter kan du ...
  \version 1.0
  \date    15. november 2009 14:58:20
  \author  Administrator User
*/

class jajPreviewNewsletterType extends eZDataType
{
    const DATA_TYPE_STRING = "jajpreviewnewsletter";

    function jajPreviewNewsletterType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, "Newsletter Preview",
          array( 'serialize_supported' => true) );
    }

    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute ) {
      if ( $currentVersion != false )
      {
          $dataText = $originalContentObjectAttribute->content();
          $contentObjectAttribute->setContent( $dataText );
      }
      else
      {
          $default = array( 'email' => "", "status" => "" );
          $contentObjectAttribute->setContent( $default );
      }
    }

    /* Validates input on content object level */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) {
      //$classAttribute = $contentObjectAttribute->contentClassAttribute();
        
      if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) ) {
        $email = $http->postVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
        $trimmedEmail = trim( $email );
        
        if ( $trimmedEmail == "" ) {
          return eZInputValidator::STATE_ACCEPTED;
        }
        
        if ( !eZMail::validate( $trimmedEmail ) ) {
            $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                 'The email address is not valid.' ) );
            return eZInputValidator::STATE_INVALID;
        }
      }
      
      return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches all variables from the object
     \return true if fetching of class attributes are successfull, false if not
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute ) {
      if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) ) {
          $data = $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) );
          $content = $contentObjectAttribute->content();
          $content["email"] = $data;
          $contentObjectAttribute->setContent( $content );          
          return true;
      }
      return false;
    }

    /*!
     Returns the content. Either stored one or a new empty one
    */
    function objectAttributeContent( $contentObjectAttribute ) {
        $value = $contentObjectAttribute->attribute( 'data_text' );
        $list = explode( ',', $value );
        $content = array("email" => "", "status" => "");

        if( count($list) == 2) {
          $content["email"] = $list[0];
          $content["status"] = $list[1];
        }
        return $content;
    }

    function storeObjectAttribute( $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();
        $value = is_array( $content ) ? implode( ',', array_values( $content ) ) : "";
        $contentObjectAttribute->setAttribute( "data_text", $value );
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return "";
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    function fromString( $contentObjectAttribute, $string )
    {
        return $contentObjectAttribute->setAttribute( 'data_text', $string );
    }
    
    /*!
     Returns the value as it will be shown if this attribute is used in the object name pattern.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( "data_text" ) ) != '';
    }

    function isInformationCollector()
    {
        return false;
    }

    function sortKey( $contentObjectAttribute )
    {
        return strtolower( $contentObjectAttribute->attribute( 'data_text' ) );
    }

    function sortKeyType()
    {
        return 'string';
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
    
    /*!
     \return true if the datatype can be indexed
    */
    function isIndexable()
    {
        return false;
    }

    function deleteStoredObjectAttribute( $objectAttribute, $version = null )
    {

    }
    
    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters ) {
      switch ( $action ) {
        case "send_preview": {
          
          $module = $parameters['module'];
          var_dump( $module->obj); //viewData() );
          var_dump( $obj );
          
          /*
          $classAttribute = $contentObjectAttribute->contentClassAttribute();
          var_dump($http);
          var_dump($action);
          var_dump($contentObjectAttribute);
          var_dump
          //$classAttribute = $contentObjectAttribute->contentClassAttribute();
          //$module = $classAttribute->currentModule();
          $module = $parameters['module'];
          //var_dump($module);
          
          //var_dump($parameters);
          $ObjectVersion = 26;
          $ObjectID = 99;
          $contentObject = ezContentObjectVersion::fetchVersion(  $ObjectVersion ,$ObjectID);
          
          $tpl =& templateInit();
          $tpl->setVariable('object', $contentObject);
          //$tpl->setVariable('newsletter', $newsletter);
          $tpl->fetch( 'design:eznewsletter/newsletter_preview.tpl' );
*/

/*
          $cacheFileArray = array( 'cache_dir' => false, 'cache_path' => false );
          $NodeID = 101;
          
//$Module = $Params['Module'];
$tpl = templateInit();
$LanguageCode = $Params['Language'];
$ViewMode = "full";
$Offset = $Params['Offset'];
//$ini = eZINI::instance();
$Year = $Params['Year'];
$Month = $Params['Month'];
$Day = $Params['Day'];
$viewParameters = array( 'offset' => $Offset,
                                 'year' => $Year,
                                 'month' => $Month,
                                 'day' => $Day,
                                 'namefilter' => false );
$viewParameters = array_merge( $viewParameters, $UserParameters );
$collectionAttributes = false;
        if ( isset( $Params['CollectionAttributes'] ) )
          $collectionAttributes = $Params['CollectionAttributes'];
        
        $validation = array( 'processed' => false, 'attributes' => array() );

        if ( isset( $Params['AttributeValidation'] ) )
          $validation = $Params['AttributeValidation'];
          
        $localVars = array( "cacheFileArray", "NodeID",  "Module", "tpl",
                                "LanguageCode",  "ViewMode", "Offset", "ini",
                                "cacheFileArray", "viewParameters", "collectionAttributes",
                                "validation" );


 

          $args = compact( $localVars );

          // the false parameter will disable generation of the 'binarydata' entry
          $data = eZNodeviewfunctions::contentViewGenerate( false, $args ); 
  */      
          $tpl = templateInit();
          
          $EditVersion = 26;
          $pathIdentificationString = "";
          $ObjectID = 99;
          $parentNodeID = 2;
          $virtualNodeID = null;
          $pathString = "";
          $depth = 2;
          $objectName = "OMG";
          
          $node = new eZContentObjectTreeNode();
          $node->setAttribute( 'contentobject_version', $EditVersion );
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
          
          $contentObject=null;
          $LanguageCode=null;
          $viewParameters =array();
          
          $contentObject = eZContentObject::fetch( $ObjectID );
          
          
          $Result = eZNodeviewfunctions::generateNodeViewData( $tpl, $node, $contentObject, $LanguageCode, 'full', 0);
          var_dump($Result);
          
          
          //var_dump( $module->run("versionview") );
          $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                               'The email address is not valid.' ) );
        }
      }
    }
}

eZDataType::register( jajPreviewNewsletterType::DATA_TYPE_STRING, "jajPreviewNewsletterType" );
?>