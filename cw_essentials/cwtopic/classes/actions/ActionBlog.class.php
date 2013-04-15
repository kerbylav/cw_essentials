<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Topics.
*   Copyright © 2012-13 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

class PluginCWTopic_ActionBlog extends PluginCWTopic_Inherit_ActionBlog
{

    protected function AjaxResponseComment()
    {
        $res=parent::AjaxResponseComment();
        $oUserCurrent=$this->User_GetUserCurrent();
        if ($oUserCurrent && count($this->Message_GetError()) == 0)
        {
            $aActAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($oUserCurrent->getId(), 'topic', getRequest('idTarget'), array('indirect','favority'), 1);
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

}
?>