<form method="post" action={'/content/action'|ezurl()}>
    <input type="hidden" name="NodeID" value="{$parent_node.node_id}"/>
    <input type="hidden" name="ContentNodeID" value="{$parent_node.node_id}"/>
    <input type="hidden" name="ContentObjectID" value="{$parent_node.contentobject_id}"/>
    <input type="hidden" name="ViewMode" value="full"/>
    <input type="hidden" name="ClassID" value="{fetch( 'class', 'list', hash( 'class_filter', array( $class ) ) )[0].id}"/>
	{if $redirect}
    <input type="hidden" name="RedirectIfDiscarded" value="{$redirect}"/>
	<input type="hidden" name="RedirectURIAfterPublish" value="{$redirect}"/>
	{/if}
    {if fetch( content, prioritized_languages )[0].locale}
    <input type="hidden" name="ContentLanguageCode" value="{fetch( content, prioritized_languages )[0].locale}"/>
    {/if}
    <input class="button" type="submit" name="NewButton" value="{$value|i18n( 'jajnewsletter' )}"/>               
</form>

