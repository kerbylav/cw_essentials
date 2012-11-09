<ul class="nav nav-pills nav-pills-profile">
{if $oUserCurrent && $oUserProfile && $oUserCurrent->getId()==$oUserProfile->getId()}
{foreach from=$aCWMenuData item=aData key=sKey name=updateData}
				<li {if $aParams[1]==$sKey}class="active"{/if}><a href="{router page='profile'}{$oUserProfile->getLogin()}/watcher/{$sKey}/">{$aData.menu_title}{if $aData.count} ({$aData.count}){/if}</a></li>
{/foreach}				
{/if}
</ul>
