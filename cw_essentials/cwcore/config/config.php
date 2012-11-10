<?php
/*-------------------------------------------------------
*
*   Comment Watcher. The Core.
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

// Действия "отвечу позже" и "не буду отвечать" добавляются в шаблонах или динамически?
$config['template_actions']=false;

// Период автоматического обновления панельки активности в секундах. Если равно 0 - не обновлять автоматически. Не стоит ставить слишком маленькие значения.
$config['autoupdate_period']=0;

// Отключить действие "не буду отвечать". В этом режиме ответ вам считается прочитанным, если вы переходите к нему, кликнув по числу ответов.
$config['no_wont_reply']=false;

// Положение панели комментов. Могут быть комбинации:
// top - верх
// bottom - низ
// left - слева
// right - справа
// и ориентации
// horizontal - горизонтальная
// vertical - вертикальная
// например: top left horizonal - горизонтальная панель в левом верхнем углу страницы
// по умолчанию стоит правый нижний вертикальный
// при этом если будет указано horizontal, то будет правый нижний горионтальный, 
// если просто top - то правый верхний вертикальный и т.п.
$config['panel_position']='right bottom vertical';

if (Config::Get('view.skin')=='simple')
    $config['template_actions']=true;

// Для действий "отвечу позже" и "не буду отвечать" должен обрабатываться свой хук для размещения в шаблоне
$config['separate_template_actions']=false;

// Группы наблюдения
$config['watch_groups']=array(
    'direct'=>array(
        'title'=>'commentwatcher_panel_title_direct',
        'menu_title'=>'commentwatcher_menu_title_direct',
        'order'=>300,
    ),
    'later'=>array(
        'title'=>'commentwatcher_panel_title_later',
        'menu_title'=>'commentwatcher_menu_title_later',
        'order'=>200,
    ),
    'activity'=>array(
        'title'=>'commentwatcher_panel_title_activity',
        'menu_title'=>'commentwatcher_menu_title_activity',
        'order'=>100,
    ),
);


Config::Set('db.table.watcher_data', '___db.table.prefix___commentwatcher_watcher_data');

Config::Set('router.page.commentwatcher', 'PluginCWCore_ActionCW');

return $config;
?>