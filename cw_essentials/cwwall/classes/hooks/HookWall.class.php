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
 * Регистрация хука для вывода меню списка комментов слежения
 *
 */
class PluginCWWall_HookWall extends Hook
{

    public function RegisterHook()
    {
        $oUserCurrent=$this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;
        
        $this->AddHook('template_html_head_end', 'DoSetWall');
        $this->AddHook('wall_add_after', 'DoAddWall');
    }

    public function DoAddWall($aParams)
    {
        $this->PluginCWWall_Worker_ProcessAddComment($aParams['oWall']);
    }

    public function DoSetWall()
    {
        global $sWallScriptData;
        
        if ($sWallScriptData)
            return $sWallScriptData;
        else
            return "";
    }

}
?>