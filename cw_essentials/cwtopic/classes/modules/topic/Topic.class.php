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
 * Наследованный  модуль топиков
 *
 */
class PluginCWTopic_ModuleTopic extends PluginCWTopic_Inherit_ModuleTopic
{

	/**
	 * Удаляет свзяанные с топика данные
	 *
	 * @param  int  $iTopicId
	 * @return bool
	 */
    public function DeleteTopicAdditionalData($iTopicId,$aPhotos=array()) {
	    $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByContainerTypeAndContainerId('topic', $iTopicId);
	    foreach ($aAnswer as $oAnswer)
	        $oAnswer->delete();
	    // Чистим кэш
	    $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('PluginCWCore_ModuleWatcher_EntityData_delete'));
	    return parent::DeleteTopicAdditionalData($iTopicId,$aPhotos);
	}
}