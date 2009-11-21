<div class="survey">

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Newsletter list"|i18n('jajnewsletter')}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

 
{def $nodes=fetch( 'content', 'list', hash( 
  	'parent_node_id', 	$newsletter_issues_node,
  	'class_filter_type',  	'include',
        	'class_filter_array', 	array( 'newsletter_issue' ),
          'sort_by',        	array( 'published', false() ),
  	'offset',		$view_parameters.offset,
  	'limit',		$view_parameters.limit
  	) )
       $node_count=fetch( 'content', 'list_count', hash(
  	'parent_node_id',       $newsletter_issues_node,
  	'class_filter_type',    'include',
          'class_filter_array',   array( 'newsletter_issue' )
  	) )
	
	$parent_node=fetch( 'content', 'node', hash( 'node_id', $newsletter_issues_node ) )
}

<table class="list" cellspacing="0">
<tr>
    <th>{"Name"|i18n('jajnewsletter')}</th>
    <th>{"Subject"|i18n('jajnewsletter')}</th>
    <th>{"Created"|i18n('jajnewsletter')}</th>
    <th>{"Status"|i18n('jajnewsletter')}</th>
    <th class="tight">{"# sent"|i18n('jajnewsletter')}</th>
    <th class="tight">Preview</th>
    <th class="tight">Deliver</th>
    <th class="tight">&nbsp;</th>
</tr>

{foreach $nodes as $node}
<tr>
	<td>
		<a href={$node.url|ezurl()}>{$node.name|wash}</a>
	</td>
	<td>
		{$node.data_map.subject.content|wash()}
	</td>
	<td>
	  {$node.object.published|l10n( shortdatetime )}
	</td>
	<td>
		  {attribute_view_gui attribute=$node.data_map.status}
		  
	</td>
	<td class="right">
	    {* 
	        0 = Draft
	        1 = In progress 
	    *}
	    
	    {if $node.data_map.status.data_text|eq(1)}
	        {fetch( 'newsletter', 'delivery_count', 
	            hash( 'newsletter_issue_object_id', $node.contentobject_id, status, 1 ) )} /
	    {/if}
	    {if $node.data_map.status.data_text|gt(0)}
	        {fetch( 'newsletter', 'delivery_count', hash( 'newsletter_issue_object_id', $node.contentobject_id ) )}
	    {/if}
	</td>
	<td class="nowrap">
		<a href={concat( 'newsletter/newsletter_preview/', $node.node_id )|ezurl} 
		  title="{'Send preview to <%email%>.'|i18n( 'jajnewsletter',, hash( '%email%', $node.data_map.preview_email.content|wash))}">
		  {'Send preview'|i18n( 'jajnewsletter' )}
		</a>
	</td>
	<td class="nowrap">
	  <a href={concat( 'newsletter/newsletter_deliver/', $node.node_id )|ezurl} 
		  title="{'Send preview to <%email%>.'|i18n( 'jajnewsletter',, hash( '%email%', $node.data_map.preview_email.content|wash))}">
		  {'Deliver'|i18n( 'jajnewsletter' )}
		</a>
	</td>
    <td>
        {include uri='design:newsletter/buttons/edit.tpl' node=$node redirect='/newsletter/newsletter_list'}
    </td>
</tr>
{/foreach}
</table>

{undef $nodes}

<div class="context-toolbar">
  {include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/newsletter/newsletter_list'
         item_count=$node_count
         view_parameters=$view_parameters
         item_limit=$view_parameters.limit}
</div>
  
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">

{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
  
<div class="block">
  {include uri='design:newsletter/buttons/action.tpl' 
          parent_node=$parent_node class="newsletter_issue" value="New newsletter" redirect="/newsletter/newsletter_list"}

  <div class="break"></div>
</div>

{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

</div>
</div>
