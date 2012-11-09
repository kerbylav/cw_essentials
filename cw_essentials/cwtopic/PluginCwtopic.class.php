<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Topics.
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

class PluginCWTopic extends PluginCWMod
{
    protected $sModType='topic';
    
    protected $aInherits=array('action'=>array('ActionBlog'),'module'=>array('ModuleTopic'));
}
?>