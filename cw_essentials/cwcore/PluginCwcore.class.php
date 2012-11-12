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

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin'))
{
    die('Hacking attempt!');
}

class PluginCWCore extends Plugin
{

    protected $aInherits=array('module'=>array('ModuleComment', 'ModuleViewer'),'action'=>array('ActionProfile'));


    /**
	 * Активация плагина "Слежение за комментами".
	 * Создание таблицы в базе данных при ее отсутствии.
	 */
    public function Activate()
    {
        if (!$this->isTableExists('prefix_commentwatcher_watcher_data'))
        {
            /**
			 * При активации выполняем SQL дамп
			 */
            $this->ExportSQL(dirname(__FILE__) . '/install.sql');
        }
        return true;
    }

    /**
	 * Деактивация плагина "Слежение за комментами".
	 * Проверка на присутсвие модов
	 */
    public function Deactivate()
    {
        $aMods=$this->PluginCWCore_Watcher_getMods();
        if (count($aMods) > 0)
        {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.cwcore.commentwatcher_mods_still_active'), $this->Lang_Get('error'), true);
            return false;
        }
        
        return true;
    }

    /**
	 * Инициализация плагина
	 */
    public function Init()
    {
        $this->Viewer_AppendStyle($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__,'css/panel.css'));
        $this->Viewer_AppendStyle($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__,'css/add.css'));
        $this->Viewer_AppendScript($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__,'js/hook.js'));
        $this->Viewer_AppendScript($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__,'js/core.js'));
    }
}

class PluginCWMod extends Plugin
{

    /**
     * Тип мода. Задается в конфиге и каджого плагина мода и должен инициализироваться в каждом новом классе плагина мода - topic, talk, etc
     * 
     * @var String
     */
    protected $sModType='';

    /**
     * Инициализируем плагин. Устанавливаем тип мода из конфига и регистрируем мод в ядре слежения
     * 
     * @see Plugin::Init()
     */
    public function Init()
    {
        $sClassName=get_class($this);
        $this->PluginCWCore_Watcher_RegisterMod($this->sModType, $sClassName);
    }

    /**
	 * Активация плагина мода.
	 * Добавление типа контейнера.
	 */
    public function Activate()
    {
        $sClassName=get_class($this);
        $sPluginName=$this->PluginCWCore_Worker_GetPluginName($sClassName);
        $aPlugins=$this->Plugin_GetList();
        $ss=Config::LoadFromFile(Config::Get('path.root.server').'/plugins/'.$sPluginName.'/config/config.php',false);
        $sVer=$ss->Get('min_core_version');
        if (version_compare($sVer, (string) $aPlugins['cwcore']['property']->version, '>'))
        {
            $this->Message_AddErrorSingle('Need cwcore plugin of version '. $sVer. ' or higher', $this->Lang_Get('error'), true);
            return false;
        }
        $this->addEnumType(Config::Get('db.table.watcher_data'), 'container_type', $this->sModType);
        return true;
    }

    /**
     * Возвращает тип мода
     */
    public function getModType()
    {
        return $this->sModType;
    }
}

?>