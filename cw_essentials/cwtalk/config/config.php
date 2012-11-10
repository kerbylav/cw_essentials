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

$config=array();

// Минимальная версия ядра слежения
$config['min_core_version']='1.0.1';

// Тип контейнеров комментов
$config['mod_type']='talk';

// Создаем свои группы слежения
$wg=Config::Get('plugin.cwcore.watch_groups');

if (version_compare(LS_VERSION, "0.5.1", '>'))
{
    $wg['talk_direct']=array('title'=>'plugin.cwtalk.commentwatcher_panel_title_talk_direct', 'menu_title'=>'plugin.cwtalk.commentwatcher_menu_title_talk_direct', 'order'=>500);
    $wg['talk_activity']=array('title'=>'plugin.cwtalk.commentwatcher_panel_title_talk_activity', 'menu_title'=>'plugin.cwtalk.commentwatcher_menu_title_talk_activity', 'order'=>500);
}
else
{
    $wg['talk_direct']=array('title'=>'commentwatcher_panel_title_talk_direct', 'menu_title'=>'commentwatcher_menu_title_talk_direct', 'order'=>500);
    $wg['talk_activity']=array('title'=>'commentwatcher_panel_title_talk_activity', 'menu_title'=>'commentwatcher_menu_title_talk_activity', 'order'=>500);
}


Config::Set('plugin.cwcore.watch_groups', $wg);

// Распределяем комменты по группам
$config['grouping']=array(
    'direct'=>'talk_direct',
    'newtalk'=>'talk_direct',
    'later'=>'later',
    'indirect'=>'talk_activity',
    'favority'=>'talk_activity',
);

return $config;
?>