{if count($aUpdateData)>0}
{foreach from=$aUpdateData item=aData key=sKey name=updateData}
{if $aData.count>0}
<a href={if $aData.url}"{$aData.url}"{else}"#" onclick="return false;"{/if} class='{$sKey}' title='{$aData.title}'>{$aData.count}</a>
{/if}
{/foreach}
{/if}