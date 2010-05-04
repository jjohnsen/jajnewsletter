<div class="content-view-full">
    <div class="class-newsletter">

        <h1>{'Newsletter sign up'|i18n('extension/jajnewsletter/signup')}: {$node.data_map.name.content|wash()}</h1>

        {if $success|not()}
            <div class="attribute-short">
                {attribute_view_gui attribute=$node.data_map.description}
            </div>
        {/if}

        {if $error} 
        <div class="warning">
            {if $error.name}
                <p>{'Name is missing'|i18n('extension/jajnewsletter/signup')}</p>
            {/if}
            {if $error.email}
                <p>{'E-mail is missing or invalid'|i18n('extension/jajnewsletter/signup')}</p>
            {/if}
            {if $error.fatal}
                <p>{'Fatal error occured, please contact site admin'|i18n('extension/jajnewsletter/signup')}</p>
            {/if}
            {if $error.removed_by_admin}
                <p>{'Sorry, you do not have permission to sign up to this newsletter'|i18n('extension/jajnewsletter/signup')}</p>
            {/if}
        </div>
        {/if}

        {if $success}
            <div class="notice">
            	{if and( $node.data_map.signup_message, $node.data_map.signup_message.has_content )}
                	{attribute_view_gui attribute=$node.data_map.signup_message}
                {else}
                	<h2>{'Thanks for your subscription!'|i18n('extension/jajnewsletter/signup')}</h2>
                {/if}
                <p>
                	<a href={ezroot()}>{'Back to the webpage'|i18n('extension/jajnewsletter/signup')}</a>
                </p>
            </div>
        {else}
        <form method="post" action="">
        <table>
        <tbody>
        <tr>
            <td style="padding-right: 1em;" width="50%">
                <h2>{'Your name'|i18n('extension/jajnewsletter/signup')} *</h2>
                <div class="attribute-name">
                    <input class="box" size="20" name="name" value="{$name|wash()}" type="text">                
                </div>
            </td>
            <td>
                <h2>{'Your e-mail'|i18n('extension/jajnewsletter/signup')} * </h2>
                <div class="attribute-email">
                    <input class="box" size="20" name="email" value="{$email|wash()}" type="text">
                </div>
            </td>
        </tr>                
        </table>
        
        <div class="content-action">
            <input class="defaultbutton" name="SignupButton" value="{'Sign up'|i18n('extension/jajnewsletter/signup')}" type="submit">
        </div>
        </form>
        {/if}
    </div>
</div>
