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
 * Наследованный  модуль подписок. Задаем чистку кэша.
 */
class PluginCWCore_ModuleSubscribe extends PluginCWCore_Inherit_ModuleSubscribe
{
    public function AddSubscribe($oSubscribe)
    {
        $res = parent::AddSubscribe($oSubscribe);
        if ($res !== false)
        {
            $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("subscribe_{$oSubscribe->getTargetType()}_change_{$oSubscribe->getTargetId()}"));
        }

        return $res;
    }

    public function UpdateSubscribe($oSubscribe)
    {
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("subscribe_{$oSubscribe->getTargetType()}_change_{$oSubscribe->getTargetId()}"));
        return parent::UpdateSubscribe($oSubscribe);
    }

}

?>