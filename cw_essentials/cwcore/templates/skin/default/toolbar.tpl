{if $oUserCurrent}
    <section id='commentwatcher-toolbar' class="toolbar-update">
        <div id="commentwatcher-update-activity" class="update-comments" onclick="ls.cw.core.updatePanel('{$aBlock.params.sCWCurContainerType}',{$aBlock.params.iCWCurContainerId}); return false;"><span class="update-activity"></span></div>
        <div id='commentwatcher-activities'>
            {$sCWPanelContent}
        </div>
    </section>
{/if}
	
