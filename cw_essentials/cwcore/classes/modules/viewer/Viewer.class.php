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

class PluginCWCore_ModuleViewer extends PluginCWCore_Inherit_ModuleViewer
{
    public function GetAssignedAjaxVar($sName)
    {
	return $this->aVarsAjax[$sName];
    }
}
?>