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
 * Модуль слежения
 *
 */
class PluginCWTopic_ModuleWorker extends PluginCWCore_ModuleWorker
{

    /**
     *
     */
    public function ProcessAddComment($oCommentNew)
    {
        $oCommentParent = null;
        if ($oCommentNew->getPid())
            $oCommentParent = $this->Comment_GetCommentById($oCommentNew->getPid());
        $oTopic = $this->Topic_GetTopicById($oCommentNew->getTargetId());

        // Если не топик
        if (!$oTopic)
            return;

        // По какой-то причине нет залогиненого пользователя
        $oUserCurrent = $this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;

        $aUsers = $this->PluginCWCore_Watcher_GetUsersBySubscribe($oTopic->getId(), 'topic_new_comment', true);

        // Получаем список пользователей у которых топик в избранном
        if (Config::Get('plugin.cwtopic.use_favourites'))
        {
            $aUsers = $this->PluginCWCore_Watcher_GetUsersByFavority($oTopic->getId(), 'topic', true);
        }

        $aUsers = array_unique(array_merge(array($oTopic->getUserId()), $aUsers));

        // Добавляем им в активность
        foreach ($aUsers as $iUser)
        {
            // Не надо добавлять пользователю, делающему коммент...
            if ($iUser == $oUserCurrent->getId())
                continue;

            // А может такой топик уже есть в активности?
            $aAnswer = $this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($iUser, $this->sModType, $oTopic->getId(), array('indirect', 'favority'), 1);

            // Дошли сюда? Тогда добавляем!
            if (count($aAnswer) == 0)
            {
                $oAnswer = Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData');
                $oAnswer->setCommentId($oCommentNew->getId());
                $oAnswer->setContainerId($oCommentNew->getTargetId());
                $oAnswer->setContainerType($this->sModType);
                $oAnswer->setReplierId($oCommentNew->getUserId());
                $oAnswer->setDateAdd(date("Y-m-d H:i:s"));

                $oAnswer->setOwnerId($iUser);

                // Если автор топика, то ему - непрямой ответ, остальным - активность в избранном
                if ($oTopic->getUserId() == $iUser)
                    $oAnswer->setCommentType('indirect');
                else
                    $oAnswer->setCommentType('favority');
                $oAnswer->save();
            }
        }

        $iUser = $oUserCurrent->getId();

        // Если мы отвечаем на коммент другого пользователя - надо удалить прошлую активность
        if ($oCommentParent)
        {
            $aAnswer = $this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndCommentIdAndCommentActive($iUser, $this->sModType, $oCommentParent->getId(), 1);
            foreach ($aAnswer as $oAnswer)
            {
                $oAnswer->delete();
            }
        }

        // Если ответ на собственный коммент
        if ($oCommentParent && $oCommentNew->getUserId() == $oCommentParent->getUserId())
            return;

        // Если это не ответ на коммент и ответ на собственный топик
        if (!$oCommentParent && $oTopic->getUserId() == $oCommentNew->getUserId())
            return;

        // Прямой ответ на коммент пользователю, или новый коммент в топике пользователя
        $oAnswer = Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData');
        $oAnswer->setCommentId($oCommentNew->getId());
        $oAnswer->setContainerType($this->sModType);
        $oAnswer->setCommentType('direct');
        if ($oCommentParent)
            $oAnswer->setCommentedId($oCommentParent->getId());
        $oAnswer->setContainerId($oCommentNew->getTargetId());
        $oAnswer->setReplierId($oCommentNew->getUserId());
        $oAnswer->setDateAdd(date("Y-m-d H:i:s"));

        if ($oCommentParent)
        {
            $oAnswer->setOwnerId($oCommentParent->getUserId());
            $this->Viewer_AssignAjax('sParentId', $oCommentParent->getId());
        }
        else
            $oAnswer->setOwnerId($oTopic->getUserId());

        $oAnswer->save();

        // Удаляем лишнюю непрямую активность в топике
        $aAnswer = $this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($oAnswer->getOwnerId(), $this->sModType, $oCommentNew->getTargetId(), array('indirect', 'favority'), 1);
        foreach ($aAnswer as $oAnswer)
        {
            $oAnswer->delete();
        }

        return true;
    }

    public function GetUrl($oAnswer)
    {
        $sLink = "";
        if (Config::Get('module.comment.nested_per_page'))
            $sLink = Router::GetPath('comments') . $oAnswer->getCommentId() . "/";
        else
        {
            $oTopic = $this->Topic_GetTopicById($oAnswer->getContainerId());
            if ($oTopic)
                $sLink = $oTopic->getUrl() . '#comment' . $oAnswer->getCommentId();
        }

        return $sLink;
    }

    public function GetContainerForListHTML($iContainerId, $aAnswer, $aGroup)
    {
        $oTopic = $this->Topic_GetTopicById($iContainerId);
        if ($oTopic)
        {
            $oTopic->setCountCommentNew(count($aAnswer));
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign("oUserCurrent", $this->User_GetUserCurrent());
            $oViewer->Assign("oTopic", $oTopic);
            $oViewer->Assign("aAnswer", $aAnswer);
            $oViewer->Assign("aGroup", $aGroup);
            return $oViewer->Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'topic.tpl'));
        }
    }

}

?>