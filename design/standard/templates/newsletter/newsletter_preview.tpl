<div class="newsletter">

<div class="context-block">

<div class="message-feedback">
<h2>
	<span class="time">[{currentdate()|l10n( shortdatetime )}]</span> 
	{'Preview was sent to %email%'|i18n( 'jajnewsletter',, hash( '%email%', $preview_email ))}
</h2>

<p>{'Please study the result in the preview email before delivering the newsletter.'|i18n( 'jajnewsletter' )}</p>
<p><a href={'newsletter/newsletter_list'|ezurl()}>Back</a></p>
</div>

</div>
</div>
