<form method="post" action={'/content/action'|ezurl()}>
    <input type="hidden" name="NodeID" value="{$node.node_id}"/>
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}"/>
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}"/>
    {if $redirect}
    <input type="hidden" name="RedirectIfDiscarded" value="{$redirect}" />
    <input type="hidden" name="RedirectURIAfterPublish" value="{$redirect}" />
    {/if}
    {if fetch( content, prioritized_languages )[0].locale}
    <input type="hidden" name="ContentObjectLanguageCode" value="{fetch( content, prioritized_languages )[0].locale}"/>
    {/if}
    
    <input type="image" name="EditButton" src={'edit.gif'|ezimage} value="Submit" alt="{'Edit'|i18n( 'design/admin/node/view/full' )}">
</form>