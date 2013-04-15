{if $oUserCurrent && $oUserProfile && $oUserCurrent->getId()==$oUserProfile->getId()}
	<li {if $aParams[0]=='watcher'}class="active"{/if}>
		<a href="{router page='profile'}{$oUserProfile->getLogin()}/watcher/">{$aLang.plugin.cwcore.menu_main}{if $aActivityTotalCount} ({$aActivityTotalCount}){/if}</a>
		{if $sAction=='commentwatcher'}
			<ul class="sub-menu">
{foreach from=$aCWMenuData item=aData key=sKey name=updateData}
				<li {if $aParams[0]==$sKey}class="active"{/if}><a href="{router page='commentwatcher'}{$oUserProfile->getLogin()}/{$sKey}/">{$aData.menu_title}{if $aData.count} ({$aData.count}){/if}</a></li>
{/foreach}				
			</ul>
		{/if}
	</li>
{/if}