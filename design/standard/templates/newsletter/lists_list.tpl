<div class="newsletter">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Subscription lists"|i18n('jajnewsletter')}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{def $nodes=fetch( 'content', 'list', hash( 
  	'parent_node_id', 	$subscription_lists_node,
  	'class_filter_type',  	'include',
        	'class_filter_array', 	array( 'subscription_list' ),
          'sort_by',        	array( 'name', true() ),
  	'offset',		$view_parameters.offset,
  	'limit',		$view_parameters.limit
  	) )
       $node_count=fetch( 'content', 'list_count', hash(
  	'parent_node_id',       $subscription_lists_node,
  	'class_filter_type',    'include',
          'class_filter_array',   array( 'subscription_list' )
  	) )

	$parent_node=fetch( 'content', 'node', hash( 'node_id', $subscription_lists_node ) )
}

<table class="list" cellspacing="0">
<tr>
    <th>{"Name"|i18n('jajnewsletter')}</th>
    <th class="tight">{"Subscribers"|i18n('jajnewsletter')}</th>
    <th class="tight">&nbsp;</th>
</tr>

{foreach $nodes as $node}
<tr>
	<td>
		<a href={concat( 'newsletter/lists_view/', $node.node_id )|ezurl}>{$node.name|wash}</a>
	</td>
	<td class="right">
		{fetch( 'content', 'reverse_related_objects_count', 
		  hash( 'object_id', $node.contentobject_id, 
		        'attribute_identifier', 'subscription_user/subscriptions', 'all_relations', false() ) 
		)}
	</td>
	<td>
	    {include uri='design:newsletter/buttons/edit.tpl' node=$node redirect='/newsletter/lists_list'}
	</td>
</tr>
{/foreach}
</table>

{undef $nodes}

<div class="context-toolbar">
  {include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/newsletter/lists_list'
         item_count=$node_count
         view_parameters=$view_parameters
         item_limit=$view_parameters.limit}
</div>
  
{* DESIGN: Content END *}</div></div></div>

{* Button bar for remove and update priorities buttons. *}
<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
  
<div class="block">
        {include uri='design:newsletter/buttons/action.tpl' 
          parent_node=$parent_node class="subscription_list" value="New subscription list" redirect="/newsletter/lists_list"}
  <div class="break"></div>
</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>
</div>
