<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Topics.
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
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin'))
{
    die('Hacking attempt!');
}

class PluginCWTopic extends PluginCWMod
{
    protected $sModType='topic';
    
    protected $aInherits=array('action'=>array('ActionBlog'),'module'=>array('ModuleTopic'));
}
?>