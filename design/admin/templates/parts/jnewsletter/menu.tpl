{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Newsletter'|i18n( 'jajnewsletter/menu' )}</h4>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">
<ul>
    <li><div><a href={'newsletter/newsletter_list'|ezurl()}>{'Newsletters'|i18n( 'jajnewsletter/menu' )}</a></div>
    <li><div><a href={'newsletter/lists_list'|ezurl()}>{'List management'|i18n( 'jajnewsletter/menu' )}</a></div></li>
    {* <li><div><a href={'newsletter/users_list'|ezurl()}>{'Subscribers'|i18n( 'jajnewsletter/menu' )}</a></div></li> *}
</ul>

{*
<div style="color: #aaa; text-align:right">
    <small>
        {cache-block expiry=86400}
        Version: 1.2.3<br/>
        <a href="#">Latest version: 1.4.5</a><br/>
        
        [{currentdate()|l10n( shortdatetime )}]
        {/cache-block}
    </small>
</div>
*}
{* DESIGN: Content END *}</div></div></div></div></div></div>
