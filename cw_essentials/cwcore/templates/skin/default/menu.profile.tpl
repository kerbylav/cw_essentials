{if $oUserCurrent && $oUserProfile && $oUserCurrent->getId()==$oUserProfile->getId()}
	<li {if $vLS10}{if $aParams[0]=='watcher'}class="active"{/if}{else}{if $sAction=='commentwatcher'}class="active"{/if}{/if}>
		<a href="{if $vLS10}{router page='profile'}{$oUserProfile->getLogin()}/watcher/{else}{router page='commentwatcher'}{$oUserProfile->getLogin()}/{/if}">{if $vLS10}{$aLang.plugin.cwcore.commentwatcher_menu_main}{else}{$aLang.commentwatcher_menu_main}{/if}{if $aActivityTotalCount} ({$aActivityTotalCount}){/if}</a>
		{if $sAction=='commentwatcher'}
			<ul class="sub-menu">
{foreach from=$aCWMenuData item=aData key=sKey name=updateData}
				<li {if $aParams[0]==$sKey}class="active"{/if}><a href="{router page='commentwatcher'}{$oUserProfile->getLogin()}/{$sKey}/">{$aData.menu_title}{if $aData.count} ({$aData.count}){/if}</a></li>
{/foreach}				
			</ul>
		{/if}
	</li>
{/if}