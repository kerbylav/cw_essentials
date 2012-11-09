{assign var="oUser" value=$oTalk->getUser()}

<div class="topic">
	<h2 class="title"><a href='{router page='talk'}read/{$oTalk->getId()}/'>Письмо: {$oTalk->getTitle()|escape:'html'}</a></h2>

	<div class="content">
		{$oTalk->getText()|truncate:300:"..."}
	</div>

	<ul class="info">
		<li class="username"><a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></li>
		<li class="date">{date_format date=$oTalk->getDate()}</li>
		{assign var="oAnswer" value=current($aAnswer)}
		<li class="comments-link"><a href='{router page='talk'}read/{$oTalk->getId()}/#comments'>{$oTalk->getCountComment()}<span>+{count($aAnswer)}</span></a></li>
	</ul>
</div>

