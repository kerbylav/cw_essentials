<?php
/*-------------------------------------------------------
*
*   Comment Watcher. The wall.
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
 * Наследованный  модуль топиков
 *
 */
class PluginCWWall_ModuleWall extends PluginCWWall_Inherit_ModuleWall
{

    public function GetWallAdditionalData($aWallId,$aAllowData=null)
    {
        $aResult=parent::GetWallAdditionalData($aWallId, $aAllowData);
        
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return $aResult;
        
        $iUserId=$oUserCurrent->getId();
        if (is_array($aResult) and count($aResult) > 0)
        {
            $aList=array();
            foreach ($aResult as $idd => $oWall)
            {
                $aList[]=$oWall->getId();
            }
            $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndCommentActiveAndNcommentIdInAndCommentTypeIn($iUserId, 1, $aList, array('lswall', 'lswall_direct'));
            
            foreach ($aAnswer as $oAnswer)
            {
                if ($oAnswer->getCommentType() == 'lswall_direct')
                    $aResult[$oAnswer->getNcommentId()]->setAnswerDirect(1);
                else
                {
                    
                    $aResult[$oAnswer->getNcommentId()]->setAnswerIndirect(1);
                }
            }
        }
        
        return $aResult;
    }
}