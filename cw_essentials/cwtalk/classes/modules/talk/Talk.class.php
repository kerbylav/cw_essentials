<?php
/*-------------------------------------------------------
*
*   Comment Watcher
*   Copyright © 2012-13 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

/**
 * Наследованный  модуль личной почты
 *
 */
class PluginCWTalk_ModuleTalk extends PluginCWTalk_Inherit_ModuleTalk
{

    /**
	 * Добавляет юзера к разговору(теме)/ Добавляем слежение.
	 *
	 * @param ModuleTalk_EntityTalkUser $oTalkUser
	 * @return unknown
	 */
    public function AddTalkUser(ModuleTalk_EntityTalkUser $oTalkUser)
    {
        $res=parent::AddTalkUser($oTalkUser);
        if ($res)
        {
            $oUserCurrent=$this->User_GetUserCurrent();
            
            // Себе не надо добавлять...
            if ($oTalkUser->getUserId() != $oUserCurrent->getUserId())
            {
                $oAnswer=$this->PluginCWCore_Watcher_GetDataByOwnerIdAndContainerTypeAndContainerIdAndCommentActive($oTalkUser->getUserId(), 'talk', $oTalkUser->getTalkId(), 1);
                if (!$oAnswer)
                {
                    $oAnswer=Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData');
                    $oAnswer->setContainerType('talk');
                    $oAnswer->setCommentType('newtalk');
                    $oAnswer->setContainerId($oTalkUser->getTalkId());
                    $oAnswer->setReplierId($oTalkUser->getUserId());
                    $oAnswer->setDateAdd(date("Y-m-d H:i:s"));
                    
                    $oAnswer->setOwnerId($oTalkUser->getUserId());
                    
                    $oAnswer->save();
                }
            }
        }
        
        return $res;
    }

    /**
	 * Удаление письма из БД. Удаляем все слежения.
	 *
	 * @param unknown_type $iTalkId
	 */
    public function DeleteTalk($iTalkId)
    {
        parent::DeleteTalk($iTalkId);
        $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByContainerTypeAndContainerId('talk', $iTalkId);
        foreach ($aAnswer as $oAnswer)
            $oAnswer->delete();
	    // Чистим кэш
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('PluginCWCore_ModuleWatcher_EntityData_delete'));
    }
}
?>