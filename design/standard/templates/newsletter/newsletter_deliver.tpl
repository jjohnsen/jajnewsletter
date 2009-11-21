<div class="newsletter">

<div class="context-block">

<div class="message-feedback">
<h2>
	<span class="time">[{currentdate()|l10n( shortdatetime )}]</span> 
	{'Delivery of newsletter %newsletter% has started'|i18n( 'jajnewsletter',, hash( '%newsletter%', $newsletter_name|wash() ))}
</h2>

<p>
    {'Please be patient!'|i18n( 'jajnewsletter')}<br/>
    {'The delivery can take some time, especially if there is a large number of recipients.'|i18n( 'jajnewsletter' )}<br/>
    {'The newsletter issue will change status to Archived when delivery is done.'|i18n( 'jajnewsletter' )}
</p>
<p><a href={'newsletter/newsletter_list'|ezurl()}>Back</a></p>
</div>

</div>
</div>
