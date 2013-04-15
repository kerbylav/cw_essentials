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

/**
 * Регистрация хука для вывода меню списка комментов слежения
 *
 */
class PluginCWCore_HookWatcher extends Hook
{

    public function RegisterHook()
    {
        $oUserCurrent = $this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;

        $this->AddHook('template_html_head_end', 'DoSetComments');
        $this->AddHook('template_body_begin', 'DoSetPanel');
        $this->AddHook('template_menu_profile', 'IncludeMenuProfile');
        $this->AddHook('template_profile_sidebar_menu_item_last', 'IncludeMenuProfile');
        $this->AddHook('template_prepare_menu_watcher', 'PrepareMenuWatcher');
        if (Config::Get('plugin.cwcore.template_actions'))
        {
            if (Config::Get('plugin.cwcore.separate_template_actions'))
            {
                $this->AddHook('template_cw_insert_reply_later', 'AddCommentActionReplyLater');
                $this->AddHook('template_cw_insert_wont_reply', 'AddCommentActionWontReply');
            }
            else
            {
                $this->AddHook('template_comment_action', 'AddCommentActions');
            }
        }
    }

    public function AddCommentActions($aParams)
    {
        $this->Viewer_Assign('oCWActionComment', $aParams['comment']);
        $this->Viewer_Assign('oConfig', Config::GetInstance());
        $res = $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'action_wont_reply.tpl'));
        $res .= $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'action_reply_later.tpl'));
        return $res;
    }

    public function AddCommentActionReplyLater($aParams)
    {
        $this->Viewer_Assign('oConfig', Config::GetInstance());
        $this->Viewer_Assign('oCWActionComment', $aParams['comment']);
        return $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'action_reply_later.tpl'));
    }

    public function AddCommentActionWontReply($aParams)
    {
        $this->Viewer_Assign('oConfig', Config::GetInstance());
        $this->Viewer_Assign('oCWActionComment', $aParams['comment']);
        return $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'action_wont_reply.tpl'));
    }

    public function DoSetComments()
    {
        $oUserCurrent = $this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;

        $res = "";
        $oSmarty = $this->Viewer_GetSmartyObject();
        $aComments = $oSmarty->GetVariable('aComments');
        $rnd = rand(0, 1000);
        if ($aComments)
            $aComments = $aComments->value;
        if ($aComments and is_array($aComments) and count($aComments) > 0)
        {
            $aDirect = array();
            $aLater = array();
            foreach ($aComments as $oComment)
            {
                if ($oComment->getAnswerDirect())
                    $aDirect[] = $oComment->getId();
                if ($oComment->getAnswerLater())
                    $aLater[] = $oComment->getId();
            }
            $was = false;
            if (count($aDirect) > 0)
            {
                $sa = join(",", $aDirect);
                $res .= "<script type='text/javascript'>
                aAnswerDirectComments=[{$sa}];
                </script>
                ";
                $was = true;
            }
            if (count($aLater) > 0)
            {
                $sa = join(",", $aLater);
                $res .= "<script type='text/javascript'>
                aAnswerLaterComments=[{$sa}];
                </script>
                ";
                $was = true;
            }

            $res .= "
                ";
        }

        global $sCWCurContainerType, $iCWCurContainerId;
        if (!$sCWCurContainerType)
            $sCWCurContainerType = '';
        if (!$iCWCurContainerId)
            $iCWCurContainerId = 0;

        $awr = $this->Lang_Get("plugin.cwcore.action_wont_reply");
        $arl = $this->Lang_Get("plugin.cwcore.action_reply_later");
        $res .= "
                <script type='text/javascript'>
                    if (typeof(ls.cw.core)!='undefined')
                    {
                        ls.cw.core.options.bTemplateAction=" . (Config::Get('plugin.cwcore.template_actions') ? 1 : 0) . ";
                        ls.cw.core.options.captions.reply_later='" . $arl . "';
                        ls.cw.core.options.captions.wont_reply='" . $awr . "';
                        ls.cw.core.options.autoupdate_period=" . Config::Get('plugin.cwcore.autoupdate_period') . ";
                        ls.cw.core.options.no_wont_reply=" . (Config::Get('plugin.cwcore.no_wont_reply') ? 1 : 0) . ";
                        iUserCurrent={$oUserCurrent->getId()}; sContainerType='{$sCWCurContainerType}'; iContainerId={$iCWCurContainerId};
                    }
                </script>
            <link rel='stylesheet' type='text/css' href='" . $this->PluginCWCore_Watcher_GetTemplateFileWebPath(__CLASS__, 'css/comments.css') . '?' . $rnd . "' />
                ";
        return $res;
    }

    public function DoSetPanel()
    {
        global $sCWCurContainerType, $iCWCurContainerId;

        $oUserCurrent = $this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;

        if (!$sCWCurContainerType)
            $sCWCurContainerType = '';
        if (!$iCWCurContainerId)
            $iCWCurContainerId = 0;

        $res = $this->PluginCWCore_Watcher_GetPanelContent();

        $this->Viewer_Assign('sCWPanelContent', $res);
        $this->Viewer_Assign('sCWCurContainerType', $sCWCurContainerType);
        $this->Viewer_Assign('iCWCurContainerId', $iCWCurContainerId);

        if (Config::Get('plugin.cwcore.control_panel') == 'panel')
        {
            return $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'panel.tpl'));
        }
        else
        {
            $this->Viewer_Assign('sCWToolbarPath', $this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'toolbar.tpl'));
            return $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'inject_toolbar.tpl'));
        }
    }

    protected function PrepareMenu()
    {
        $oUserCurrent = $this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;

        $oUserProfile = $this->User_GetUserByLogin(Router::GetActionEvent());
        if (!$oUserProfile)
            return;

        if ($oUserCurrent->getId() != $oUserProfile->getId())
            return;

        $aGroups = Config::Get('plugin.cwcore.watch_groups');
        uasort($aGroups, array('PluginCWCore_ModuleWatcher', 'CompareGroups'));
        $aMods = $this->PluginCWCore_Watcher_getMods();

        foreach ($aMods as $sModType => $sModClass)
        {
            if (is_array(Config::Get("plugin." . $this->PluginCWCore_Worker_GetPluginName($sModClass) . ".grouping")))
            {
                foreach (Config::Get("plugin." . $this->PluginCWCore_Worker_GetPluginName($sModClass) . ".grouping") as $key => $val)
                {
                    if ($aGroups[$val])
                    {
                        $aGroups[$val]['types'][] = $sModType;
                        $aGroups[$val]['activity'][] = $key;
                    }
                }
            }
        }
        $iGCount = 0;
        foreach ($aGroups as $sGroupName => $aGroup)
        {
            if (is_array($aGroups[$sGroupName]['types']))
                $aGroups[$sGroupName]['types'] = array_unique($aGroups[$sGroupName]['types']);
            if (is_array($aGroups[$sGroupName]['activity']))
                $aGroups[$sGroupName]['activity'] = array_unique($aGroups[$sGroupName]['activity']);
            $res = "";
            $aData = $this->PluginCWCore_Watcher_GetGroupedData($oUserProfile->getId(), $aGroups[$sGroupName]['types'], $aGroups[$sGroupName]['activity'], 1, 1);
            $aGroup['count'] = $aData['count'];
            $iGCount += $aData['count'];
            if (((substr($aGroups[$sGroupName]['title'], 0, 7) != 'plugin.')))
                $aGroup['menu_title'] = $this->Lang_Get("plugin.cwcore." . $aGroup['menu_title']);
            else
                $aGroup['menu_title'] = $this->Lang_Get($aGroup['menu_title']);

            $aGroups[$sGroupName] = $aGroup;
        }
        $this->Viewer_Assign('aActivityTotalCount', $iGCount);
        $this->Viewer_Assign('aCWMenuData', $aGroups);
        Engine::GetInstance()->Viewer_Assign('aCWMenuData', $aGroups);
    }

    public function IncludeMenuProfile()
    {
        $this->PrepareMenu();
        return $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'menu.profile.tpl'));
    }

    public function PrepareMenuWatcher($aParams)
    {
        $this->PrepareMenu();
        return $this->Viewer_Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'menu.watcher.tpl'));
    }

}

?>