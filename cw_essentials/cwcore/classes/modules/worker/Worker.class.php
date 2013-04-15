<?php
/*-------------------------------------------------------
*
*   Comment Watcher. The Core.
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
 * Абстрактный Модуль обработки
 *
 */
class PluginCWCore_ModuleWorker extends ModuleORM
{
    /**
     * Строка типа мода - topic, talk, etc. Должна устанавливаться в методе Init каждого наследуемого модуля-обработчика
     * @var String
     */
    protected $sModType;

    /**
	 * Инициализация модуля
	 */
    public function Init()
    {
        parent::Init();
        $this->sModType=Config::Get("plugin.".$this->GetPluginName(get_class($this)).".mod_type");
    }
    
    /**
     * Получает имя плагина из имени класса, преобразуя его для использования в конфиге. Например PluginCWCore будет cwcore
     * @param unknown_type $sClassName
     */
    public function GetPluginName($sClassName)
    {
        $sPluginName=preg_match('/^Plugin([\w]+)(_[\w]+)?$/Ui', $sClassName, $aMatches)?strtolower($aMatches[1]):strtolower($sClassName);
        return $sPluginName;
    }
    
    
    /**
     * Обрабатывает коммент для занесения в список
     * 
     * @param unknown_type $oCommentNew Добавляемый комментарий
     */
    public function ProcessAddComment($oCommentNew)
    {
        return true;
    }

    /**
     * Возвращает URL для перехода на коммент
     * 
     * @param unknown_type $oAnswer Объект слежения
     */
    public function GetUrl($oAnswer)
    {
        return "";
    }

    /**
     * Возвращает готовый HTML для вывода контейнера комментов в список
     * 
     * @param unknown_type $iContainerId ID контейнера комментов
     * @param unknown_type $aAnswer Список объектов слежения в этом контейнере
     * @param unknown_type $aGroup Группа наблюдения
     */
    public function GetContainerForListHTML($iContainerId, $aAnswer, $aGroup)
    {
        return "";
    }

    /**
     * Проверяет можно ли сделать действие "не буду отвечать" для коммента
     * 
     * @param unknown_type $oComment Коммент
     */
    public function WontReply($oComment)
    {
        return true;
    }
    
    /**
     * Проверяет можно ли сделать действие "отвечу позже" для коммента
     * 
     * @param unknown_type $oComment Коммент
     */
    public function ReplyLater($oComment)
    {
        return true;
    }
    
}
?>