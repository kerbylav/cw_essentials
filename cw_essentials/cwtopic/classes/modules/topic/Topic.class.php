<?php
/*-------------------------------------------------------
*
*   Comment Watcher
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
class PluginCWTopic_ModuleTopic extends PluginCWTopic_Inherit_ModuleTopic
{

	/**
	 * Удаляет свзяанные с топика данные
	 *
	 * @param  int  $iTopicId
	 * @return bool
	 */
	public function DeleteTopicAdditionalData($iTopicId) {
	    $aAnswer=$this->PluginCWCore_Watcher_GetDataItemsByContainerTypeAndContainerId('topic', $iTopicId);
	    foreach ($aAnswer as $oAnswer)
	        $oAnswer->delete();
	    // Чистим кэш
	    $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('PluginCWCore_ModuleWatcher_EntityData_delete'));
	    return parent::DeleteTopicAdditionalData($iTopicId);
	}
}