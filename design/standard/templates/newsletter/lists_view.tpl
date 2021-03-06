{def
  $parent_node=fetch( 'content', 'node', hash( 'node_id', $subscription_users_node ) )
  
	$list_node=fetch( 'content', 'node', hash( 'node_id', $node_id ) )
	
        $nodes=fetch( 'content', 'list', hash(
	    'parent_node_id', 	  $subscription_users_node,
	    'class_filter_type',  'include',
            'class_filter_array', array( 'subscription_user' ),
            'extended_attribute_filter', hash(
                'id', 'eorfilter',
                'params', array(
                    array('subscription_user/subscriptions', $list_node.contentobject_id)
                )
            ),
            'sort_by',        array( 
                'attribute',
                true(),
                'subscription_user/email' 
            ),
            'offset',		$view_parameters.offset,
            'limit',		$view_parameters.limit
	) )
	
	$node_count=fetch( 'content', 'list_count', hash(
    'parent_node_id',       $subscription_users_node,
    'class_filter_type',    'include',
    'class_filter_array',   array( 'subscription_user' ),
    'extended_attribute_filter', hash(
      'id', 'eorfilter',
      'params', array(
        array('subscription_user/subscriptions', $list_node.contentobject_id)
      )
    )
  ) )
}
<div class="newsletter">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Subscription list %list_name%"|i18n('jajnewsletter',, hash( '%list_name%', $list_node.name|wash()))}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr">
<div class="context-information">
<p class="modified">{'Last modified'|i18n( 'design/admin/node/view/full' )}: {$list_node.object.modified|l10n(shortdatetime)}, <a href={$node.object.current.creator.main_node.url_alias|ezurl}>{$list_node.object.current.creator.name|wash}</a></p>
<p class="translation">{$list_node.object.current_language_object.locale_object.intl_language_name}&nbsp;<img src="{$list_node.object.current_language|flag_icon}" alt="{$language_code}" style="vertical-align: middle;" /></p>
<div class="break"></div>
</div>

<div class="box-content">
    <table cellspacing="6" width="100%">
    <tr>
        <th align="left">{'Name'|i18n('jajnewsletter')}</th>
        <th align="left">{'Creator'|i18n('jajnewsletter')}</th>
        <th align="left">{'Created'|i18n('jajnewsletter')}</th>
    </tr>
    <tr>
        <td>{$list_node.name|wash()}</td>
        <td></td>
        <td>{$list_node.object.published|l10n( shortdatetime )}</td>
    </table>
    
    <div class="block">
        <label>{'Description'|i18n('jajnewsletter')}:</label>
        {attribute_view_gui attribute=$list_node.data_map.description}
    </div>
    <div class="block">
        <label>{'Registration URL'|i18n('jajnewsletter')}:</label>
        {concat('newsletter/sign_up/', $list_node.remote_id)|ezurl(no)}
    </div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>


{* CHILD LIST BEGIN *}


<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{"Subscriber list"|i18n('jajnewsletter')}</h2>
<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>


{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

  <table class="list" cellspacing="0">
  <tr>
      <th class="tight">ID</th>
      <th>{"Name"|i18n('jajnewsletter')}</th>
      <th>{"Email"|i18n('jajnewsletter')}</th>
      <th>{"Created"|i18n('jajnewsletter')}</th>
      <th>{"Status"|i18n('jajnewsletter')}</th>
      <th class="tight">&nbsp;</th>
  </tr>

  {foreach $nodes as $node}
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
  	  {$node.object.published|l10n( shortdatetime )}
  	</td>
  	<td>
  	  {attribute_view_gui attribute=$node.data_map.status}
  	</td>
  	<td>
  	    {include uri='design:newsletter/buttons/edit.tpl' node=$node redirect=concat('newsletter/lists_view/', $node_id)}
  	</td>
  </tr>
  {/foreach}
  </table>

  <div class="context-toolbar">
    {include name=navigator
           uri='design:navigator/google.tpl'
           page_uri=concat('newsletter/lists_view/', $node_id)
           item_count=$node_count
           view_parameters=$view_parameters
           item_limit=$view_parameters.limit}
  </div>
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

    <table>
    <tr>
        <td>
            {include uri='design:newsletter/buttons/action.tpl' 
              parent_node=$parent_node class="subscription_user" value="New subscription" 
              redirect=concat('newsletter/lists_view/', $node_id)}
        </td>
        <td>
            {include uri='design:newsletter/buttons/action.tpl' action='newsletter/users_import'
              parent_node=$list_node class="subscription_user" value="Import from CSV"
              redirect=concat('newsletter/lists_view/', $node_id)}
        </td>
    </tr>
    </table>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

</div>
</div>
