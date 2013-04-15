<?php
/*-------------------------------------------------------
*
*   Comment Watcher. The Core.
*   Copyright © 2012-13 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

class PluginCWCore_ActionProfile extends PluginCWCore_Inherit_ActionProfile
{

    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^watcher$/i', '/^(.)+$/i', '/^(page(\d+))?$/i', 'EventCommentWatcher');
        $this->AddEventPreg('/^[\w\-\_]+$/i', '/^watcher$/i', '/^$/i', 'EventCommentWatcher');
        parent::RegisterEvent();
    }

    protected function EventCommentwatcher()
    {
        if (!$this->oUserCurrent)
        {
            return parent::EventNotFound();
        }

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
        
        if ($this->oUserProfile->getId() != $this->oUserCurrent->getId())
        {
            return parent::EventNotFound();
        }
        $iPage=$this->GetParamEventMatch(2, 2)?$this->GetParamEventMatch(2, 2):1;
        
        if (version_compare(LS_VERSION, "0.5.1", '>'))
            $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.cwcore.commentwatcher_menu_main'));
        else
            $this->Viewer_AddHtmlTitle($this->Lang_Get('commentwatcher_menu_main'));
        
        $sGroupName=Router::GetParam(1);
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
                    Router::Location(Router::GetPath('profile') . $sUserLogin . "/" . 'watcher' . '/' . $sTGroupName . '/');
                
                $aTGroups[$sTGroupName]=$aTGroup;
            }
            
            if (!$aGroups[$sTGroupName])
            {
                return parent::EventNotFound();
            }
            else
                Router::Location(Router::GetPath('profile') . $sUserLogin . "/" . 'watcher' . '/' . $sFGroupName . '/');
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

    public function EventShutdown()
    {
        parent::EventShutdown();
        
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
        $this->Viewer_Assign('oUserCurrent', $this->oUserCurrent);
        $this->Viewer_Assign('iCountTopicUser', $iCountTopicUser);
        $this->Viewer_Assign('iCountCommentUser', $iCountCommentUser);
        $this->Viewer_Assign('vLS10', version_compare(LS_VERSION, "0.5.1", '>'));
    }
}
?>