<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Talks.
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

class PluginCWTalk extends PluginCWMod
{

    protected $sModType='talk';

    protected $aInherits=array('action'=>array('ActionTalk'), 'module'=>array('ModuleTalk'));

    /**
	 * Активация плагина мода.
	 * Добавление типа контейнера.
	 */
    public function Activate()
    {
        if (parent::Activate())
        {
            $this->addEnumType(Config::Get('db.table.watcher_data'), 'container_type', 'talk');
            $this->addEnumType(Config::Get('db.table.watcher_data'), 'comment_type', 'newtalk');
            return true;
        }
        else
            return false;
    }

}
?>