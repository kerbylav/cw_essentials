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
 * Модуль слежения
 *
 */
class PluginCWCore_ModuleWatcher extends ModuleORM
{

    protected $oMapper;

    protected $aMods = array();

    /**
     * Инициализация модуля
     */
    public function Init()
    {
        parent::Init();
        $this->oMapper = Engine::GetMapper(__CLASS__);
    }

    /**
     * Получаем список пользователей, у которых заданный элемент находится в избранном
     *
     * @param unknown_type $sTargetId ID элемента избранного
     * @param unknown_type $sTargetType Тип элемента избранного - topic, talk, comment
     * @param unknown_type $bOnlyId Если true - возвращает только массив ID пользователей, если false - массив обектов пользователей
     */
    public function GetUsersByFavority($sTargetId, $sTargetType, $bOnlyId = false)
    {
        $ck = "{$sTargetType}_favourite_users_{$sTargetId}";
        if (false === ($data = $this->Cache_Get($ck)))
        {
            $data = $this->oMapper->GetUsersByFavority($sTargetId, $sTargetType);
            $aCacheKeys = array();
            foreach ($data as $id)
            {
                $aCacheKeys = "favourite_{$sTargetType}_change_user_{id}";
            }
            $this->Cache_Set(
                $data,
                $ck,
                array_merge(array("favourite_{$sTargetType}_change"), $aCacheKeys),
                60 * 60 * 24 * 1
            );
        }

        if ($bOnlyId)
            return $data;
        else
            return $this->User_GetUsersAdditionalData($data);
    }

    /**
     * Получаем список пользователей, у которых заданный элемент находится в заданой подписке
     *
     * @param unknown_type $sTargetId ID элемента избранного
     * @param unknown_type $sTargetType Тип подписки - например topic_new_comment
     * @param unknown_type $bOnlyId Если true - возвращает только массив ID пользователей, если false - массив обектов пользователей
     */
    public function GetUsersBySubscribe($sTargetId, $sTargetType, $bOnlyId = false)
    {
        $ck = "{$sTargetType}_subscribed_users_{$sTargetId}";
        if (false === ($data = $this->Cache_Get($ck)))
        {
            $data = $this->oMapper->GetUsersBySubscribe($sTargetId, $sTargetType);
            $this->Cache_Set(
                $data,
                $ck,
                array("subscribe_{$sTargetType}_change_{$sTargetId}"),
                60 * 60 * 24 * 1
            );
        }
        if ($bOnlyId)
            return $data;
        else
            return $this->User_GetUsersAdditionalData($data);
    }

    /**
     * Функция для сравнения порядка групп
     *
     * @param unknown_type $g1
     * @param unknown_type $g2
     * @return number
     */
    static function CompareGroups($g1, $g2)
    {
        if ($g1['order'] == $g2['order'])
            return 0;

        return $g1['order'] < $g2['order'] ? 1 : -1;
    }

    /**
     * Возвращает HTML код для панельки активности
     */
    public function GetPanelContent()
    {
        global $sCWCurContainerType, $iCWCurContainerId;

        $oUserCurrent = $this->User_GetUserCurrent();

        if (!$oUserCurrent)
            return "";

        $oViewer = $this->Viewer_GetLocalViewer();

        $aGroups = Config::Get('plugin.cwcore.watch_groups');
        uasort($aGroups, array(__CLASS__, 'CompareGroups'));

        $aMods = $this->getMods();

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
        foreach ($aGroups as $sGroupName => $aArray)
        {
            $aGroups[$sGroupName]['count'] = 0;
            if (isset($aGroups[$sGroupName]['types']) && is_array($aGroups[$sGroupName]['types']))
                $aGroups[$sGroupName]['types'] = array_unique($aGroups[$sGroupName]['types']);
            if (isset($aGroups[$sGroupName]['activity']) && is_array($aGroups[$sGroupName]['activity']))
                $aGroups[$sGroupName]['activity'] = array_unique($aGroups[$sGroupName]['activity']);
            if (version_compare(LS_VERSION, "0.5.1", '>') && ((substr($aGroups[$sGroupName]['title'], 0, 7) != 'plugin.')))
                $aGroups[$sGroupName]['title'] = $this->Lang_Get("plugin.cwcore." . $aGroups[$sGroupName]['title']);
            else
                $aGroups[$sGroupName]['title'] = $this->Lang_Get($aGroups[$sGroupName]['title']);
            if (isset($aArray['types']) && is_array($aArray['types']))
            {
                $aAnswer = $this->PluginCWCore_ModuleWatcher_GetDataItemsByOwnerIdAndCommentTypeInAndCommentActiveAndContainerTypeIn($oUserCurrent->getId(), $aArray['activity'], 1, $aArray['types'], array('#order' => array('container_type', 'container_id', 'date_add')));

                $aGroups[$sGroupName]['count'] = count($aAnswer);
                $oAnswer = null;
                if (count($aAnswer) > 0)
                {
                    $was = false;
                    $first = true;
                    reset($aAnswer);
                    do
                    {
                        $oLast = $oAnswer;
                        $oAnswer = current($aAnswer);

                        if ($oAnswer && $oAnswer->getContainerType() == $sCWCurContainerType && $oAnswer->getContainerId() == $iCWCurContainerId)
                            $was = true;

                        if (!next($aAnswer))
                        {
                            if ($first)
                            {
                                $was = true;
                                $first = false;
                                reset($aAnswer);
                            }
                            else
                                $oAnswer = null;
                        }
                    } while ($oAnswer && (!$was || ($was && $oAnswer->getContainerType() == $sCWCurContainerType && $oAnswer->getContainerId() == $iCWCurContainerId)));

                    if (($oAnswer || $oLast) && ($oAnswer != $oLast))
                    {
                        if (!$oAnswer)
                            $oAnswer = $oLast;

                        $sFuncName = $aMods[$oAnswer->getContainerType()] . '_ModuleWorker_GetUrl';
                        $aGroups[$sGroupName]['url'] = $this->$sFuncName($oAnswer);
                    }
                    else
                        $aGroups[$sGroupName]['url'] = '';
                }
            }
        }
        $oViewer->Assign('aUpdateData', $aGroups);
        if (Config::Get('plugin.cwcore.control_panel') == 'panel')
        {
            return $oViewer->Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'panel_content.tpl'));
        }
        else
        {
            return $oViewer->Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'toolbar_content.tpl'));
        }
    }

    /**
     * Возвращает список зарегистрированных модов
     */
    public function GetMods()
    {
        return $this->aMods;
    }

    public function GetGroupedData($iUserId, $aTypes, $aActType, $iPage, $iPerPage)
    {
        $iCount = 0;
        $data = $this->oMapper->GetGroupedData($iUserId, $aTypes, $aActType, $iCount, $iPage, $iPerPage);
        return array('result' => $data, 'count' => $iCount);
    }

    /**
     * Регистрирует новый мод
     *
     * @param unknown_type $type Тип мода - topic, talk, etc
     * @param unknown_type $class Класс мода
     */
    public function RegisterMod($type, $class)
    {
        if (!isset($this->aMods[$type]))
        {
            $this->aMods[$type] = $class;
        }
    }

    public function GetTemplateFilePath($sPluginClass, $sFileName)
    {
        $sPP = Plugin::GetTemplatePath($sPluginClass);
        $fName = $sPP . $sFileName;
        if (file_exists($fName))
            return $fName;

        $aa = explode("/", $sPP);
        array_pop($aa);
        array_pop($aa);
        $aa[] = 'default';
        $aa[] = '';
        return join("/", $aa) . $sFileName;
    }

    public function GetTemplateFileWebPath($sPluginClass, $sFileName)
    {
        return str_replace(Config::Get('path.root.server'), Config::Get('path.root.web'), $this->GetTemplateFilePath($sPluginClass, $sFileName));
    }

}

?>