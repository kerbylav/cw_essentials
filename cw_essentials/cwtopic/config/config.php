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

$config=array();

// Минимальная версия ядра слежения
$config['min_core_version']='2.0.0';

// Тип контейнеров комментов
$config['mod_type']='topic';

// Следить ли за новыми комментами во всех топиках, помещенных в избранное?
$config['use_favourites']=false;

// Распределяем комменты по группам
$config['grouping']=array(
    'direct'=>'direct',
    'later'=>'later',
    'indirect'=>'activity',
    'favority'=>'activity',
);

return $config;
?>