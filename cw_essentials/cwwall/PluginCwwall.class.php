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
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin'))
{
    die('Hacking attempt!');
}

class PluginCWWall extends PluginCWMod
{
    protected $sModType='lswall';
    
    protected $aInherits=array('action'=>array('ActionProfile'),'module'=>array('ModuleWall'));

    public function Activate()
    {
        if (parent::Activate())
        {
            $this->addEnumType(Config::Get('db.table.watcher_data'), 'comment_type', 'lswall');
            $this->addEnumType(Config::Get('db.table.watcher_data'), 'comment_type', 'lswall_direct');
            //            $this->addEnumType(Config::Get('db.table.favourite'), 'target_type', 'cwlswall');
            return true;
        }
        else
            return false;
    }
    
    /**
	 * Инициализация плагина
	 */
    public function Init()
    {
        parent::Init();
        $this->Viewer_AppendStyle($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__,'css/wall.css'));
        $this->Viewer_AppendScript($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__,'js/wall.js'));
        
        return true;
    }
}
?>