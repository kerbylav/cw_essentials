<?php
/*-------------------------------------------------------
*
*   Comment Watcher. The Core.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: imthinker.ru/commentwatcher
*   Contact e-mail: kerbylav@gmail.com
*
---------------------------------------------------------
*/

class PluginCWCore_ActionCW extends Action
{

    /**
     * Логин юзера из УРЛа
     *
     * @var unknown_type
     */
    protected $sUserLogin=null;

    /**
     * Объект юзера чей профиль мы смотрим
     *
     * @var unknown_type
     */
    protected $oUserProfile=null;

    /**
     * Объект текущего залогиненого юзера 
     *
     * @var unknown_type
     */
    protected $oUserCurrent=null;

    public function Init()
    {
        $this->oUserCurrent=$this->User_GetUserCurrent();
        if (!$this->oUserCurrent)
            return parent::EventNotFound();
    }

    /**
	 * Регистрируем евенты
	 *
	 */
    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^ajaxwontreply$/', 'EventWontReply');
        $this->AddEventPreg('/^ajaxreplylater$/', 'EventReplyLater');
        $this->AddEventPreg('/^ajaxupdatepanel$/', 'EventUpdatePanel');
        $this->AddEventPreg('/^[\w\-\_]+$/i',  '/^(.)+$/i', '/^(page(\d+))?$/i', 'EventCommentWatcher');
        $this->AddEventPreg('/^[\w\-\_]+$/i',  '/^$/i', 'EventCommentWatcher');
    }

    protected function EventCommentwatcher()
    {
        /**
		 * Получаем логин из УРЛа
		 */
        $sUserLogin=$this->sCurrentEvent;
        
        /**
		 * Проверяем есть ли такой юзер
		 */
        if (!($this->oUserProfile=$this->User_GetUserByLogin($sUserLogin)))
        {
            return parent::EventNotFound();
        }
        
        $this->oUserCurrent=$this->User_GetUserCurrent();
        
        if ($this->oUserProfile->getId() != $this->oUserCurrent->getId())
        {
            return parent::EventNotFound();
        }
        $iPage=$this->GetParamEventMatch(1, 2)?$this->GetParamEventMatch(1, 2):1;
        
        if (version_compare(LS_VERSION, "0.5.1", '>'))
            $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.cwcore.commentwatcher_menu_main'));
        else
            $this->Viewer_AddHtmlTitle($this->Lang_Get('commentwatcher_menu_main'));
        
        $sGroupName=Router::GetParam(0);
        $aGroups=Config::Get('plugin.cwcore.watch_groups');
        uasort($aGroups, array('PluginCWCore_ModuleWatcher', 'CompareGroups'));
        
        $aMods=$this->PluginCWCore_Watcher_getMods();
        
        foreach ($aMods as $sModType => $sModClass)
        {
            foreach (Config::Get("plugin." . $this->PluginCWCore_Worker_GetPluginName($sModClass) . ".grouping") as $key => $val)
            {
                if ($aGroups[$val])
                {
                    $aGroups[$val]['types'][]=$sModType;
                    $aGroups[$val]['activity'][]=$key;
                }
            }
        }
        foreach ($aGroups as $sKey => $aArray)
        {
            $aGroups[$sKey]['types']=array_unique($aGroups[$sKey]['types']);
            $aGroups[$sKey]['activity']=array_unique($aGroups[$sKey]['activity']);
        }
        
        reset($aGroups);
        if (!$sGroupName)
        {
            $aTGroups=$aGroups;
            $sFGroupName=null;
            foreach ($aTGroups as $sTGroupName => $aTGroup)
            {
                if (!$sFGroupName)
                    $sFGroupName=$sTGroupName;
                
                if (is_array($aTGroups[$sTGroupName]['types']))
                    $aTGroups[$sTGroupName]['types']=array_unique($aTGroups[$sTGroupName]['types']);
                if (is_array($aTGroups[$sTGroupName]['activity']))
                    $aTGroups[$sTGroupName]['activity']=array_unique($aTGroups[$sTGroupName]['activity']);
                $res="";
                $aData=$this->PluginCWCore_Watcher_GetGroupedData($this->oUserProfile->getId(), $aTGroups[$sTGroupName]['types'], $aTGroups[$sTGroupName]['activity'], 1, 1);
                if ($aData['count'] > 0)
                    Router::Location(Router::GetPath('commentwatcher') . $sUserLogin . '/' . $sTGroupName . '/');
                
                $aTGroups[$sTGroupName]=$aTGroup;
            }
            
            if (!$aGroups[$sTGroupName])
            {
                return parent::EventNotFound();
            }
            else
                Router::Location(Router::GetPath('commentwatcher') . $sUserLogin . "/" . $sFGroupName . '/');
        }
        
        $res="";
        $aData=$this->PluginCWCore_Watcher_GetGroupedData($this->oUserProfile->getId(), $aGroups[$sGroupName]['types'], $aGroups[$sGroupName]['activity'], $iPage, 10);
        foreach ($aData['result'] as $aEl)
        {
            $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerIdAndContainerTypeAndCommentTypeInAndCommentActive($this->oUserProfile->getId(), $aEl['id'], $aEl['type'], $aGroups[$sGroupName]['activity'], 1);
            $sFuncName=$aMods[$aEl['type']] . "_Worker_GetContainerForListHTML";
            $res.=$this->$sFuncName($aEl['id'], $aAnswer, $aGroups[$sGroupName]);
        }
        
        $this->Viewer_Assign('aData', $aData);
        $this->Viewer_Assign('sData', $res);
        
        if (version_compare(LS_VERSION, "0.5.1", '>') && ((substr($aGroups[$sGroupName]['title'], 0, 7) != 'plugin.')))
            $this->Viewer_AddHtmlTitle($this->Lang_Get("plugin.cwcore." . $aGroups[$sGroupName]['title']));
        else
            $this->Viewer_AddHtmlTitle($this->Lang_Get($aGroups[$sGroupName]['title']));
        
        if (version_compare(LS_VERSION, "0.5.1", '>'))
            $this->SetTemplate($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'actions/ActionProfile/topics_v10.tpl'));
        else
            $this->SetTemplate($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'actions/ActionProfile/topics.tpl'));
    }

    protected function EventWontReply()
    {
        $this->Viewer_SetResponseAjax('json');
        
        $oUserCurrent=$this->User_GetUserCurrent();
        
        if (!$oUserCurrent)
            return false;
        
        $idComment=getRequest('idComment', null, 'post');
        if (!($oComment=$this->Comment_GetCommentById($idComment)))
        {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
        
        $aMods=$this->PluginCWCore_Watcher_GetMods();
        $sClassName=$aMods[$oComment->getTargetType()];
        if ($sClassName)
        {
            $sFuncName=$sClassName . '_ModuleWorker_WontReply';
            if ($this->$sFuncName($oComment))
            {
                $oAnswer=$this->PluginCWCore_Watcher_GetDataByOwnerIdAndContainerTypeAndCommentIdAndCommentTypeAndCommentActive($oUserCurrent->getId(), $oComment->getTargetType(), $idComment, 'direct', 1);
                if ($oAnswer)
                    $oAnswer->delete();
                
                $oAnswer=$this->PluginCWCore_Watcher_GetDataByOwnerIdAndContainerTypeAndCommentIdAndCommentTypeAndCommentActive($oUserCurrent->getId(), $oComment->getTargetType(), $idComment, 'later', 1);
                if ($oAnswer)
                    $oAnswer->delete();
            
            }
        }
    }

    protected function EventReplyLater()
    {
        $this->Viewer_SetResponseAjax('json');
        
        $oUserCurrent=$this->User_GetUserCurrent();
        
        if (!$oUserCurrent)
            return false;
        
        $idComment=getRequest('idComment', null, 'post');
        if (!($oComment=$this->Comment_GetCommentById($idComment)))
        {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
        
        $aMods=$this->PluginCWCore_Watcher_GetMods();
        $sClassName=$aMods[$oComment->getTargetType()];
        if ($sClassName)
        {
            $sFuncName=$sClassName . '_ModuleWorker_ReplyLater';
            if ($this->$sFuncName($oComment))
            {
                $oAnswer=$this->PluginCWCore_Watcher_GetDataByOwnerIdAndContainerTypeAndCommentIdAndCommentTypeAndCommentActive($oUserCurrent->getId(), $oComment->getTargetType(), $idComment, 'direct', 1);
                if ($oAnswer)
                {
                    $oAnswer->delete();
                }
                
                // Если уже...
                $oAnswer=$this->PluginCWCore_Watcher_GetDataByOwnerIdAndContainerTypeAndCommentIdAndCommentTypeAndCommentActive($oUserCurrent->getId(), $oComment->getTargetType(), $idComment, 'later', 1);
                if ($oAnswer)
                {
                    return;
                }
                else
                {
                    // Если в активности есть ссылка на топик, то удаляем ее
                    $aFavAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerIdAndCommentType($oUserCurrent->getId(), $oComment->getTargetId(), 'activity');
                    foreach ($aFavAnswer as $oFavAnswer)
                        $oFavAnswer->delete();
                        
                        // Прямой ответ на коммент пользователю, или новый коммент в топике пользователя
                    $oAnswer=Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData');
                    $oAnswer->setCommentId($oComment->getId());
                    $oAnswer->setContainerType($oComment->getTargetType());
                    $oAnswer->setCommentType('later');
                    $oAnswer->setContainerId($oComment->getTargetId());
                    $oAnswer->setReplierId($oComment->getUserId());
                    $oAnswer->setDateAdd(date("Y-m-d H:i:s"));
                    $oAnswer->setOwnerId($oUserCurrent->getId());
                    
                    $oAnswer->save();
                }
            }
        }
    }

    protected function EventUpdatePanel()
    {
        global $sCWCurContainerType, $iCWCurContainerId;
        
        $this->Viewer_SetResponseAjax('json');
        $oUserCurrent=$this->User_GetUserCurrent();
        
        if (!$oUserCurrent)
        {
            $this->Viewer_AssignAjax('sCWPanelContent', "");
        }
        else
        {
            $sCWCurContainerType=getRequest('sContainerType');
            $iCWCurContainerId=getRequest('iContainerId');
            $this->Viewer_Assign('vLS10', version_compare(LS_VERSION, "0.5.1", '>'));
            $this->Viewer_AssignAjax('sCWPanelContent', $this->PluginCWCore_Watcher_GetPanelContent());
        }
    }

    /**
	 * Выполняется при завершении работы экшена
	 *
	 */
    public function EventShutdown()
    {
        if (!$this->oUserProfile)
        {
            return;
        }
        /**
		 * Загружаем в шаблон необходимые переменные
		 */
        $iCountTopicUser=$this->Topic_GetCountTopicsPersonalByUser($this->oUserProfile->getId(), 1);
        $iCountCommentUser=$this->Comment_GetCountCommentsByUserId($this->oUserProfile->getId(), 'topic');
        
        $this->Viewer_Assign('oUserProfile', $this->oUserProfile);
        $this->Viewer_Assign('iCountTopicUser', $iCountTopicUser);
        $this->Viewer_Assign('iCountCommentUser', $iCountCommentUser);
        $this->Viewer_Assign('vLS10', version_compare(LS_VERSION, "0.5.1", '>'));
    }
}
?>