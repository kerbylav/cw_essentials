{include file='header.tpl' menu="profile"}

{if (count($aData.result)>0)}
{$sData}
{else}
	<div class="padding">{$aLang.blog_no_topic}</div>
{/if}
{include file='footer.tpl'}

