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

$config=array();

// Минимальная версия ядра слежения
$config['min_core_version']='1.0.1';

// Тип контейнеров комментов
$config['mod_type']='topic';

// Распределяем комменты по группам
$config['grouping']=array(
    'direct'=>'direct',
    'later'=>'later',
    'indirect'=>'activity',
    'favority'=>'activity',
);

return $config;
?>