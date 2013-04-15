<?php
/*-------------------------------------------------------
*
*   Comment Watcher. Talks.
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
class PluginCWTalk_ModuleWorker extends PluginCWCore_ModuleWorker
{

    /**
     *
     */
    public function ProcessAddComment($oCommentNew)
    {
        $oCommentParent = null;
        if ($oCommentNew->getPid())
            $oCommentParent = $this->Comment_GetCommentById($oCommentNew->getPid());
        $oTalk = $this->Talk_GetTalkById($oCommentNew->getTargetId());

        // Если нет такого разговора
        if (!$oTalk)
            return;

        // По какой-то причине нет залогиненого пользователя
        $oUserCurrent = $this->User_GetUserCurrent();
        if (!$oUserCurrent)
            return;

        // Получаем список участников разговора
        $aUList = array($oTalk->getUserId());
        $aTu = $this->Talk_GetTalkUsersByTalkId($oTalk->getId());
        foreach ($aTu as $oU)
        {
            if ($oU->getUserActive() == 1)
                $aUList[] = $oU->getUserId();
        }
        $aUsers = array_unique(array_merge($aUsers, $aUList));

        // Добавляем им в активность
        foreach ($aUsers as $iUser)
        {
            // Не надо добавлять пользователю, делающему коммент...
            if ($iUser == $oUserCurrent->getId())
                continue;

            // А может такой разговор уже есть в активности?
            $aAnswer = $this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActive($iUser, $this->sModType, $oTalk->getId(), array('indirect', 'favority'), 1);

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

                if (in_array($iUser, $aUList) !== false)
                    $oAnswer->setCommentType('indirect');
                else
                    $oAnswer->setCommentType('favority');
                $oAnswer->save();
            }
        }

        $iUser = $oUserCurrent->getId();

        // Если в активности есть ссылка на это письмо как на новое - удаляем
        $oActAnswer = $this->PluginCWCore_Watcher_GetDataByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeAndCommentActive($iUser, $this->sModType, $oTalk->getId(), 'newtalk', 1);
        if ($oActAnswer)
            $oActAnswer->delete();

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

        // Пользователь на коммент которого отвечаем удалил у себя это письмо.
        if ($oCommentParent && array_search($oCommentParent->getUserId(), $aUList) === false)
            return;

        // Если это не ответ на коммент и ответ на собственный топик
        if (!$oCommentParent && $oTalk->getUserId() == $oCommentNew->getUserId())
            return;

        // Если это не ответ на коммент и ответ на собственный топик
        if (!$oCommentParent && $oTalk->getUserId() == $oCommentNew->getUserId())
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
            $oAnswer->setOwnerId($oTalk->getUserId());

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
        if ($oTalk = $this->Talk_GetTalkById($oAnswer->getContainerId()))
        {
            $sLink = Router::GetPath('talk') . 'read/' . $oAnswer->getContainerId() . '/#comment' . $oAnswer->getCommentId();
        }

        return $sLink;
    }

    public function GetContainerForListHTML($iContainerId, $aAnswer, $aGroup)
    {
        if ($oTalk = $this->Talk_GetTalkById($iContainerId))
        {
            $oTalk->setCountCommentNew(count($aAnswer));
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign("oUserCurrent", $this->User_GetUserCurrent());
            $oViewer->Assign("oTalk", $oTalk);
            $oViewer->Assign("aAnswer", $aAnswer);
            $oViewer->Assign("aGroup", $aGroup);
            return $oViewer->Fetch($this->PluginCWCore_Watcher_GetTemplateFilePath(__CLASS__, 'talk.tpl'));
        }
    }

}

?>