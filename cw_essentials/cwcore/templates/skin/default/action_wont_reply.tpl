{if !$oConfig->getValue('plugin.cwcore.no_wont_reply')}{if $oCWActionComment->getAnswerDirect() || $oCWActionComment->getAnswerLater()}<li><a href="#" class="cw-action-wont-reply" title="" onclick="ls.cw.core.toggleAnswer(this,{$oCWActionComment->getId()},'wontreply'); return false;">{if $vLS10}{$aLang.plugin.cwcore.commentwatcher_action_wont_reply}{else}{$aLang.commentwatcher_action_wont_reply}{/if}</a></li>{/if}{/if}