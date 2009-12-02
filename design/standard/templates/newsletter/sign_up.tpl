<div class="content-view-full">
    <div class="class-newsletter">

        <h1>Newsletter sign up: {$node.data_map.name.content|wash()}</h1>

        {if $success|not()}
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}

        {if $error} 
        <div class="warning">
            {if $error.name}
                <p>{'Name is missing'|i18n('jajnewsletter')}</p>
            {/if}
            {if $error.email}
                <p>{'E-mail is missing or invalid'|i18n('jajnewsletter')}</p>
            {/if}
            {if $error.fatal}
                <p>{'Fatal error occured, please contact site admin'|i18n('jajnewsletter')}</p>
            {/if}
            {if $error.removed_by_admin}
                <p>{'Sorry, you do not have permission to sign up to this newsletter'|i18n('jajnewsletter')}</p>
            {/if}
        </div>
        {/if}

        {if $success}
            <div class="notice">
                {attribute_view_gui attribute=$node.data_map.signup_message}
            </div>
        {else}
        <form method="post" action="">
        <table>
        <tbody>
        <tr>
            <td style="padding-right: 1em;" width="50%">
                <h2>Your name *</h2>
                <div class="attribute-name">
                    <input class="box" size="70" name="name" value="{$name|wash()}" type="text">                
                </div>
            </td>
            <td>
                <h2>Your e-mail * </h2>
                <div class="attribute-email">
                    <input class="box" size="70" name="email" value="{$email|wash()}" type="text">
                </div>
            </td>
        </tr>                
        </table>
        
        <div class="content-action">
            <input class="defaultbutton" name="SignupButton" value="Send skjema" type="submit">
        </div>
        </form>
        {/if}
    </div>
</div>
