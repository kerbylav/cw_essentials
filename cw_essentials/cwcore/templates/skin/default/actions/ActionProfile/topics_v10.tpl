{assign var="sidebarPosition" value='left'}
{include file='header.tpl'}

{include file='actions/ActionProfile/profile_top.tpl'}
{hook run='prepare_menu_watcher_v10'}
{if (count($aData.result)>0)}
{$sData}
{else}
	<div class="padding">{$aLang.blog_no_topic}</div>
{/if}
{include file='footer.tpl'}

