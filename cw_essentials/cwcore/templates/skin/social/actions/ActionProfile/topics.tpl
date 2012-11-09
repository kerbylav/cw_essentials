{assign var="rSidebar" value=true}
{assign var="noSidebar" value=true}
{include file='header.tpl' menu="profile"}

{assign var="oSession" value=$oUserProfile->getSession()}
{assign var="oVote" value=$oUserProfile->getVote()}

{if (count($aData.result)>0)}
{$sData}
{else}
	<div class="padding">{$aLang.blog_no_topic}</div>
{/if}
{include file='footer.tpl'}

