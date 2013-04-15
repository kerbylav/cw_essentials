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
$config['min_core_version']='2.0.0';

// Тип контейнеров комментов
$config['mod_type']='lswall';

// Создаем свои группы слежения
$wg=Config::Get('plugin.cwcore.watch_groups');

Config::Set('db.table.watcher_wall_watch', '___db.table.prefix___commentwatcher_watcher_wall_watch');

Config::Set('plugin.cwcore.watch_groups', $wg);

// Распределяем комменты по группам
$config['grouping']=array(
    'lswall_direct'=>'activity',
    'lswall'=>'activity',
);

return $config;
?>