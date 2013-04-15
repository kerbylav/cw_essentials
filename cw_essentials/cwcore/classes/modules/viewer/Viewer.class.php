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

class PluginCWCore_ModuleViewer extends PluginCWCore_Inherit_ModuleViewer
{
    public function GetAssignedAjaxVar($sName)
    {
	return $this->aVarsAjax[$sName];
    }
}
?>