<div class="content-view-full">
    <div class="class-newsletter">

        <h1>Newsletter sign up: {$node.data_map.name.content|wash()}</h1>

        {section show=$node.data_map.short_description.content.is_empty|not}
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/section} 

        {if $error} 
        <div class="warning">
            {if $error.name}
                <p>{'Name is missing'|i18n('jajnewsletter')}</p>
            {/if}
            {if $error.email}
                <p>{'E-mail is missing or invalid'|i18n('jajnewsletter')}</p>
            {/if}
        </div>
        {/if}

        <form method="post" action="">
        <table>
        <tbody>
        <tr>
            <td style="padding-right: 1em;" width="50%">
                <h2>Your name *</h2>
                <div class="attribute-name">
                    <input class="box" size="70" name="name" value="" type="text">                
                </div>
            </td>
            <td>
                <h2>Your e-mail * </h2>
                <div class="attribute-email">
                    <input class="box" size="70" name="email" value="" type="text">
                </div>
            </td>
        </tr>                
        </table>
        
        <div class="content-action">
            <input class="defaultbutton" name="ActionCollectInformation" value="Send skjema" type="submit">
            <input name="ContentNodeID" value="249" type="hidden">
            <input name="ContentObjectID" value="259" type="hidden">
            <input name="ViewMode" value="full" type="hidden">
        </div>
        </form>
    </div>
</div>
