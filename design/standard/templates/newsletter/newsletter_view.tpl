{def
    $parent_node=fetch( 'content', 'node', hash( 'node_id', $newsletter_issue_node ) )
    $issue_node=fetch( 'content', 'node', hash( 'node_id', $node_id ) )
    $relations=array()
}

{foreach $issue_node.data_map.subscription_lists.content.relation_list as $relation}
    {set $relations=$relations|append($relation.contentobject_id)}
{/foreach}

{if $relations|count()|gt(0)}
{def     
    $nodes=fetch( 'content', 'list', hash(
        'parent_node_id',               $subscription_users_node,
	'class_filter_type',            'include',
        'class_filter_array',           array( 'subscription_user' ),
        'extended_attribute_filter',    hash(
            'id', 'eorfilter',
            'params', array(
                array('subscription_user/subscriptions', $relations, 'or')
            )
        ),
        'sort_by', array(
            'attribute',
            true(),
            'subscription_user/email'
        ),
        'offset',		        $view_parameters.offset,
        'limit',		        $view_parameters.limit
    ) )
    $node_count=fetch( 'content', 'list_count', hash(
        'parent_node_id',       $subscription_users_node,
        'class_filter_type',    'include',
        'class_filter_array',   array( 'subscription_user' ),
        'extended_attribute_filter', hash(
            'id', 'eorfilter',
            'params', array(
                array('subscription_user/subscriptions', $relations, 'or')
            )
        )
    ) )
}
{/if}

<div class="newsletter">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Newsletter issue %issue_name%"|i18n('jajnewsletter',, hash( '%issue_name%', $issue_node.name|wash()))}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
    <table cellspacing="6">
    <tr>
        <td valign="top">  
            <div class="block">
                <label>Subject</label>
                {$issue_node.data_map.subject.content|wash}
            </div>
        </td>
        <td>
            <div class="block">
                <label>Recipients</label>
                {attribute_view_gui attribute=$issue_node.data_map.subscription_lists}
            </div>
        </td>
        <td valign="top">
            <div class="block">
                <label>Send preview to</label>
                {attribute_view_gui attribute=$issue_node.data_map.preview_email}
            </div>          
        </td>
        <td valign="top">
            <div class="block">
                <label>Status</label>
                {attribute_view_gui attribute=$issue_node.data_map.status}
            </div>
        </td>
    </tr>
    </table>

    <div class="block">
        <label>Preview</label>
    </div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
    {*include uri='design:newsletter/buttons/action.tpl' 
      parent_node=$parent_node class="subscription_user" value="New subscription" 
      redirect=concat('newsletter/lists_view/', $node_id)*}
    <div class="break"></div>
</div>
 
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>


{* CHILD LIST BEGIN *}

{if $relations|count()|gt(0)}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{"Recipients list"|i18n('jajnewsletter')}</h2>
<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>


{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

  <table class="list" cellspacing="0">
  <tr>
      <th class="tight">ID</th>
      <th>{"Name"|i18n('jajnewsletter')}</th>
      <th>{"Email"|i18n('jajnewsletter')}</th>
      <th>{"Status"|i18n('jajnewsletter')}</th>
      <th>{"Sent"|i18n('jajnewsletter')}</th>
      <th class="tight">&nbsp;</th>
  </tr>
  {def $delivery_status=array(
    'Pending',
    'Sent',
    'Failed',
    'Cancelled',
    'Invalid'
  )}

  {foreach $nodes as $node}
  {def $delivery=fetch( 'newsletter', 'delivery_status', 
    hash(       'newsletter_issue_object_id', $issue_node.contentobject_id, 
                'subscription_user_object_id', $node.contentobject_id))
  }
  <tr>
    <td class="right">
      {$node.node_id}  
    </td>
  	<td>
  	  {$node.data_map.name.content|wash}
  	</td>
  	<td>
  	  {$node.data_map.email.content|wash}
  	</td>
  	<td>
            {if $delivery}
                {$delivery_status[$delivery.status]|i18n('jajnewsletter')}
            {else}
                Pending delivery of newsletter
            {/if}
  	  {*$node.object.published|l10n( shortdatetime )*}
  	</td>
  	<td>
            {if $delivery.tstamp}
                {$delivery.tstamp|l10n( shortdatetime )}
            {/if}
  	</td>
  	<td>
  	    {include uri='design:newsletter/buttons/edit.tpl' node=$node redirect=concat('newsletter/newsletter_view/', $node_id)}
  	</td>
  </tr>
  {undef $delivery}
  {/foreach}
  </table>

  <div class="context-toolbar">
    {include name=navigator
           uri='design:navigator/google.tpl'
           page_uri=concat('newsletter/newsletter_view/', $node_id)
           item_count=$node_count
           view_parameters=$view_parameters
           item_limit=$view_parameters.limit}
  </div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
  
  <div class="block">
    {*include uri='design:newsletter/buttons/action.tpl' 
      parent_node=$parent_node class="subscription_user" value="New subscription" 
      redirect=concat('newsletter/lists_view/', $node_id)*}
    <div class="break"></div>
  </div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

{/if}

</div>
</div>
