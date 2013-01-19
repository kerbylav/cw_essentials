{if count($aUpdateData)>0}
<ul>
{foreach from=$aUpdateData item=aData key=sKey name=updateData}
{if $aData.count>0}
<li class='{$sKey}' title='{$aData.title}' {if $aData.url}onclick='window.location.href="{$aData.url}";'{/if}><a href={if $aData.url}"{$aData.url}"{else}"#" onclick="return false;"{/if}>{$aData.count}</a></li>
{/if}
{/foreach}
</ul>{/if}