{def $subscription_lists=fetch( 'content', 'list', hash(
    'parent_node_id',           $subscription_lists_node,
    'class_filter_type',        'include',
    'class_filter_array',       array( 'subscription_list' )
))}

<div class="newsletter">

<div class="context-block">

{if $warnings|count()|gt(0)}
<div class="message-feedback">
<h2>
        <span class="time">[{currentdate()|l10n( shortdatetime )}]</span> 
        {'Subscribers imported'|i18n( 'jajnewsletter' )}
</h2>

<p>
    {foreach $warnings as $warning}
        {$warning}<br/>
    {/foreach} 
</p>
</div>
{/if}

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">{"Import subscribers"|i18n('jajnewsletter')}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>

<form enctype="multipart/form-data" name="users_import" method="post" action={'newsletter/users_import'|ezurl}>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
    <div class="block">
        <fieldset>
            <legend>{"Add subscribers to subscription lists"|i18n( 'jajnewsletter' )}</legend>
            {foreach $subscription_lists as $list}
            <input name="SubscriptionListObjectID[]" 
                value="{$list.contentobject_id}" 
                {if $add_to_subscription_lists|contains($list.contentobject_id)}checked="checked" {/if}
                type="checkbox"> {$list.name} <br/>
            {/foreach}
        </fieldset>
    </div>

    <div class="block">
        <label>{"Upload file"|i18n( 'jajnewsletter' )}:</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="0" />
        <input name="UploadCSVFile" type="file" /><input type="submit" value="{'Upload file'|i18n( 'jajnewsletter' )}" /> 
    </div>

    <div class="block">
        <input type="checkbox" name="FirstRowIsLabel" checked="checked" 
            value="true">{'First row is label'|i18n( 'jajnewsletter' )}
    </div>
    <div class="block">
        <label>{'CSV field delimiter'|i18n( 'jajnewsletter' )}:
            <input style="text-align:center" type="text" size="1"
                maxlength="1" name="CSVDelimiter" value="{$CSVDelimiter}">
        </label>
    </div>
    
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">

<div class="block">
    <input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n( 'jajnewsletter' )}" />
{if $data|count()|gt(0)}
    <input class="button" type="submit" name="ImportButton" value="{'Import selected'|i18n( 'jajnewsletter' )}" />
{/if}
</div>
 
{* DESIGN: Control bar END *}</div></div></div></div></div></div>
</div>

{if $data|count()|gt(0)}

{* CHILD LIST BEGIN *}

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">
<h2 class="context-title">{"Recipients list"|i18n('jajnewsletter')}</h2>
<div class="header-subline"></div>
{* DESIGN: Header END *}</div></div></div></div></div></div>


{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

    <table class="list" cellspacing="0">
    <tr>
        <th class="tight center">
            <img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection'|i18n( 'jajnewsletter' )}" 
                title="{'Invert selection'|i18n( 'jajnewsletter' )}" 
                onclick="ezjs_toggleCheckboxes( document.users_import, 'RowNum[]' ); return false;" />
        </th>
        <th class="tight right">
            {'Row #'|i18n( 'jajnewsletter' )}
        </th>
        <th>{"Name"|i18n('jajnewsletter')}</th>
        <th>{"Email"|i18n('jajnewsletter')}</th>
    </tr>
  
    {foreach $data as $index => $row}
    <tr>
        <td class="center">
            <input type="checkbox" name="RowSelection[]" value="{$index}" 
                {if $row_selection|contains($index)}checked="checked"{/if}
            /> 
        </td>
        <td class="right">
            {$index|inc}
        </td>
        <td>
            {$row[0]}  
        </td>
  	<td>
  	    {$row[1]}
        </td>
    </tr>
    {/foreach}
  </table>
{* DESIGN: Content END *}</div></div></div>

</form>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
  
{* DESIGN: Control bar END *}</div></div></div></div></div></div>

</div>

{/if}

</div>
</div>
