<?php
/*-------------------------------------------------------
*
*   Comment Watcher.The wall.
*   Copyright © 2012-13 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

$config=array();

// Минимальная версия ядра слежения
$config['min_core_version']='1.0.1';

// Тип контейнеров комментов
$config['mod_type']='lswall';

// Создаем свои группы слежения
$wg=Config::Get('plugin.cwcore.watch_groups');

Config::Set('db.table.watcher_wall_watch', '___db.table.prefix___commentwatcher_watcher_wall_watch');

if (version_compare(LS_VERSION, "0.5.1", '>'))
{
//    $wg['lswall']=array('title'=>'plugin.cwtalk.commentwatcher_panel_title_talk_direct', 'menu_title'=>'plugin.cwtalk.commentwatcher_menu_title_talk_direct', 'order'=>500);
//    $wg['lswall_direct']=array('title'=>'plugin.cwtalk.commentwatcher_panel_title_talk_activity', 'menu_title'=>'plugin.cwtalk.commentwatcher_menu_title_talk_activity', 'order'=>500);
}
else
{
    die('Unsupported');
}


Config::Set('plugin.cwcore.watch_groups', $wg);

// Распределяем комменты по группам
$config['grouping']=array(
    'lswall_direct'=>'activity',
    'lswall'=>'activity',
);

return $config;
?>