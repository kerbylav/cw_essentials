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

$config=array();

// Минимальная версия ядра слежения
$config['min_core_version']='2.0.0';

// Тип контейнеров комментов
$config['mod_type']='talk';

// Следить ли за новыми .
$config['use_favorities']=true;

// Создаем свои группы слежения
$wg=Config::Get('plugin.cwcore.watch_groups');

$wg['talk_direct']=array('title'=>'plugin.cwtalk.panel_title_talk_direct', 'menu_title'=>'plugin.cwtalk.menu_title_talk_direct', 'order'=>500);
$wg['talk_activity']=array('title'=>'plugin.cwtalk.panel_title_talk_activity', 'menu_title'=>'plugin.cwtalk.menu_title_talk_activity', 'order'=>500);


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