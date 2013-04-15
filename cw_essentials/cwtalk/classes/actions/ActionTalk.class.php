<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Talks.
*   Copyright © 2012-13 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

class PluginCWTalk_ActionTalk extends PluginCWTalk_Inherit_ActionTalk
{

    protected function AjaxResponseComment()
    {
        $res=parent::AjaxResponseComment();
        $oUserCurrent=$this->User_GetUserCurrent();
        if ($oUserCurrent && count($this->Message_GetError()) == 0)
        {
            $aActAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($oUserCurrent->getId(), 'talk', getRequest('idTarget'), array('indirect','newtalk','favority'), 1);
            foreach ($aActAnswer as $oActAnswer)
                $oActAnswer->delete();
            $aComments=$this->Viewer_GetAssignedAjaxVar('aComments');
            if ($aComments and is_array($aComments) and count($aComments) > 0)
            {
                foreach ($aComments as $key => $oComment)
                {
                    $oCmt=$this->Comment_GetCommentById($oComment['id']);
                    $oComment['isAnswerDirect']=$oCmt->getAnswerDirect();
                    $oComment['isAnswerLater']=$oCmt->getAnswerLater();
                    $aComments[$key]=$oComment;
                }
                $this->Viewer_AssignAjax('aComments', $aComments);
            }
        }
        
        return $res;
    }

    protected function EventRead()
    {
        $res=parent::EventRead();
        $oUserCurrent=$this->User_GetUserCurrent();
        if ($oUserCurrent && count($this->Message_GetError()) == 0)
        {
            $sTalkId=$this->GetParam(0);
            global $sCWCurContainerType, $iCWCurContainerId;
            
            $sCWCurContainerType='talk';
            $iCWCurContainerId=$sTalkId;
            
            $aActAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($oUserCurrent->getId(), 'talk', $sTalkId, array('indirect','newtalk','favority'), 1);
            foreach ($aActAnswer as $oActAnswer)
                $oActAnswer->delete();
        }
        
        return $res;
    }

}
?>