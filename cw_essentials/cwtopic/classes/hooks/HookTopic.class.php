<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Topics
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: imthinker.ru/commentwatcher
*   Contact e-mail: kerbylav@gmail.com
*
---------------------------------------------------------
*/

/**
 * Регистрация хука для убирания топика из активности
 *
 */
class PluginCWTopic_HookTopic extends Hook
{

    protected $oUserCurrent=null;

    public function RegisterHook()
    {
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;
        
        $this->AddHook('topic_show', 'OnTopicShow');
    }

    public function OnTopicShow($aParams)
    {
        $oTopic=$aParams['oTopic'];

        
        // Если не топик
        if (!$oTopic)
            return;
        
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;
        
        $aActAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($oUserCurrent->getId(), 'topic', $oTopic->getId(), array('indirect', 'favority'), 1);
        foreach ($aActAnswer as $oActAnswer)
            $oActAnswer->delete();
        
        global $sCWCurContainerType, $iCWCurContainerId;
        
        $sCWCurContainerType='topic';
        $iCWCurContainerId=$oTopic->getId();
    }

}
?>