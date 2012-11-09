<?php
/*-------------------------------------------------------
*
*   Comment Watcher
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
 * Наследованный  модуль комментов
 *
 */
class PluginCWCore_ModuleComment extends PluginCWCore_Inherit_ModuleComment
{

    /**
	 * Добавляет коммент
	 *
	 * @param  ModuleComment_EntityComment $oComment
	 * @return bool
	 */
    public function AddComment(ModuleComment_EntityComment $oComment)
    {
        $res=parent::AddComment($oComment);
        if ($res)
        {
            $aMods=$this->PluginCWCore_Watcher_GetMods();
            $sClassName=$aMods[$oComment->getTargetType()];
            if ($sClassName)
            {
                $sFuncName=$sClassName . '_ModuleWorker_ProcessAddComment';
                $this->$sFuncName($oComment);
            }
        }
        
        return $res;
    }

    /**
	 * Обновляет статус у коммента - delete или publish. Мы ставим ответы активные и не активные
	 *
	 * @param  ModuleComment_EntityComment $oComment
	 * @return bool
	 */
    public function UpdateCommentStatus(ModuleComment_EntityComment $oComment)
    {
        $res=parent::UpdateCommentStatus($oComment);
        if ($res)
        {
            $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByCommentId($oComment->getId());
            foreach ($aAnswer as $oAnswer)
            {
                $oAnswer->setCommentActive(!$oComment->getDelete());
                $oAnswer->save();
            }
            $this->Viewer_AssignAjax('isAnswerDirect', $oComment->getAnswerDirect());
            $this->Viewer_AssignAjax('isAnswerLater', $oComment->getAnswerLater());
        }
        
        return $res;
    }

    /**
	 * Получает дополнительные данные(объекты) для комментов по их ID
	 *
	 */
    public function GetCommentsAdditionalData($aCommentId,$aAllowData=array('vote','target','favourite','user'=>array()))
    {
        $aResult=parent::GetCommentsAdditionalData($aCommentId, $aAllowData);
        
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return $aResult;
        
        $iUserId=$oUserCurrent->getId();
        if (is_array($aResult) and count($aResult)>0)
        {
            $aList=array();
            foreach ($aResult as $oComment)
            {
                $aList[]=$oComment->getId();
            }
            $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndCommentActiveAndCommentIdInAndCommentTypeIn($iUserId, 1, $aList, array('direct', 'later'));
            foreach ($aAnswer as $oAnswer)
            {
                if ($oAnswer->getCommentType() == 'direct')
                    $aResult[$oAnswer->getCommentId()]->setAnswerDirect(1);
                else 
                    if ($oAnswer->getCommentType() == 'later')
                        $aResult[$oAnswer->getCommentId()]->setAnswerLater(1);
            }
        }
        
        return $aResult;
    }
}
?>