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
 * Модуль слежения 
 *
 */
class PluginCWWall_ModuleWorker extends PluginCWCore_ModuleWorker
{

    /**
	 * 
	 */
    public function ProcessAddComment($oWallNew)
    {
        $oWallParent=null;
        if ($oWallNew->getPid())
            $oWallParent=$this->Wall_GetWallById($oWallNew->getPid());
        
        $oUser=$this->User_GetUserById($oWallNew->getWallUserId());
        
        // Пользователь не найден
        if (!$oUser)
        {
            return;
        }
        
        // По какой-то причине нет залогиненого пользователя
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
        {
            return;
        }
        
        $iUser=$oUserCurrent->getId();
        
        // Уже есть активность на этой стене
        if ($oWallParent)
        {
            $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($oWallNew->getUserId(), $this->sModType, $oWallNew->getWallUserId(), array('direct', 'indirect', 'lswall'), 1);
            if (count($aAnswer) > 0)
            {
                return;
            }
        }
        
        if (!$oWallParent && $oUserCurrent->getId() != $oWallNew->getWallUserId())
        {
            // Первый коммент на стене - сообщаем владельцу стены
            $oAnswer=Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData');
            $oAnswer->setNcommentId($oWallNew->getId());
            
            if ($oWallParent)
                $oAnswer->setNcommentedId($oWallParent->getId());
            
            $oAnswer->setContainerType($this->sModType);
            $oAnswer->setCommentType('lswall_direct');
            $oAnswer->setContainerId($oWallNew->getWallUserId());
            $oAnswer->setReplierId($oWallNew->getUserId());
            $oAnswer->setDateAdd(date("Y-m-d H:i:s"));
            $oAnswer->setOwnerId($oUser->getId());
            
            $oAnswer->save();
            
            // Подписываем автора коммента на эту ветку
            $oSubs=Engine::GetEntity('PluginCWWall_ModuleWatcher_EntityWallWatch');
            
            $oSubs->setWallId($oWallNew->getId());
            $oSubs->setUserId($oUserCurrent->getId());
            
            $oSubs->save();
            
            // Подписываем владельца стены на эту ветку
            $oSubs=Engine::GetEntity('PluginCWWall_ModuleWatcher_EntityWallWatch');
            
            $oSubs->setWallId($oWallNew->getId());
            $oSubs->setUserId($oWallNew->getWallUserId());
            
            $oSubs->save();
        }
        
        // Получаем список пользователей которые подписаны на эту ветку
        if ($oWallParent)
        {
            $aUList=array();
            $aSubs=$this->PluginCWWall_Watcher_GetWallWatchItemsByWallId($oWallParent->getId());
            
            foreach ($aSubs as $oU)
            {
                if ($oU->getUserId() != $oUserCurrent->getId())
                    $aUList[]=$oU->getUserId();
            }
            
            foreach ($aUList as $sId)
            {
                $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActiveAndNcommentedId($sId, $this->sModType, $oWallNew->getWallUserId(), array('direct', 'indirect', 'lswall','lswall_direct'), 1,$oWallParent->getId());
                if (count($aAnswer) > 0)
                {
                    continue;
                }
                
                $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActiveAndNcommentId($sId, $this->sModType, $oWallNew->getWallUserId(), array('direct', 'indirect', 'lswall','lswall_direct'), 1,$oWallParent->getId());
                if (count($aAnswer) > 0)
                {
                    continue;
                }
                
                $oAnswer=Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData');
                $oAnswer->setNcommentId($oWallNew->getId());
                
                $oAnswer->setNcommentedId($oWallParent->getId());
                
                $oAnswer->setContainerType($this->sModType);
                $oAnswer->setCommentType('lswall');
                $oAnswer->setContainerId($oWallNew->getWallUserId());
                $oAnswer->setReplierId($oWallNew->getUserId());
                $oAnswer->setDateAdd(date("Y-m-d H:i:s"));
                $oAnswer->setOwnerId($sId);
                
                $oAnswer->save();
            }
        }
        
        return true;
    }

    public function GetUrl($oAnswer)
    {
        $sLink="";
        $oUserCurrent=$this->User_GetUserCurrent();
        
        if (!$oUserCurrent)
            return "";
        
        $oWall=$this->Wall_GetWallById($oAnswer->getNcommentId());
        if ($oWall)
        {
            $oWallUser=$this->User_GetUserById($oWall->getWallUserId());
            if (!$oWallUser)
                return "";
            
            if ($oAnswer->getCommentType()=='lswall_direct')
            $sLink=Router::GetPath('profile') . $oWallUser->getLogin() . '/wall/#wall-item-' . $oWall->getId();
            else
            $sLink=Router::GetPath('profile') . $oWallUser->getLogin() . '/wall/#wall-reply-item-' . $oWall->getId();
        }
        
        return $sLink;
    }

    public function GetContainerForListHTML($iContainerId,$aAnswer,$aGroup)
    {
        /*        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return "";
        
        $oPicture=$this->PluginPicalbums_Picture_GetPictureById($iContainerId);
        if ($oPicture)
        {
            $oAlbum=$this->PluginPicalbums_Album_GetAlbumById($oPicture->getAlbumId());
            if ($oAlbum)
            {
                if ($oAlbum->getUserId() == $oUserCurrent->getId())
                    $sAdd=$oUserCurrent->getUserAlbumsWebPath();
                else
                    $sAdd=Router::GetPath(Config::Get('plugin.picalbums.main_albums_router_name'));
                
                $oViewer=$this->Viewer_GetLocalViewer();
                $oViewer->Assign("oUserCurrent", $oUserCurrent);
                $oViewer->Assign("oUserProfile", $oUserCurrent);
                $oViewer->Assign("oPicture", $oPicture);
                $oViewer->Assign("oAlbum", $oAlbum);
                $oViewer->Assign("sAlbumPathStart", $sAdd);
                $oViewer->Assign("aGroup", $aGroup);
                $oViewer->Assign('sIncludesTplPath', rtrim(Plugin::GetTemplatePath(PluginPicalbums), '/') . '/includes');
                
                return $oViewer->Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'pic.tpl'));
            }
        }*/
    
    }

}
?>