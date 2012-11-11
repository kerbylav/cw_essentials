<?php
/*-------------------------------------------------------
*
*   Comment Watcher. The wall.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: imthinker.ru/commentwatcher
*   Contact e-mail: kerbylav@gmail.com
*
---------------------------------------------------------
*/

class PluginCWWall_ActionProfile extends PluginCWWall_Inherit_ActionProfile
{

    public function EventWall()
    {
        $aResult=parent::EventWall();
        $oUserCurrent=$this->User_GetUserCurrent();
        if ($oUserCurrent && count($this->Message_GetError()) == 0)
        {
            $res="";
            $oSmarty=$this->Viewer_GetSmartyObject();
            $aWall=$oSmarty->GetVariable('aWall');
            $rnd=rand(0, 1000);
            if ($aWall)
                $aWall=$aWall->value;
                //        pr($aWall);
            $aDirect=array();
            $aIndirect=array();
            if ($aWall and is_array($aWall) and count($aWall) > 0)
            {
                foreach ($aWall as $oWall)
                {
                    if ($oWall->getAnswerDirect())
                        $aDirect[]=$oWall->getId();
                    $aLR=$oWall->getLastReplyWall();
                    foreach ($aLR as $key => $oR)
                    {
                        if ($oR->getAnswerIndirect())
                            $aDirect[]=$oWall->getId();
                    }
                }
                $aDirect=array_unique($aDirect);
                
                $was=false;
                if (count($aDirect) > 0)
                {
                    $sa=join(",", $aDirect);
                    $res.="<script type='text/javascript'>
                aDirectWall=[{$sa}];
                </script>
                ";
                    $was=true;
                }
                if (count($aIndirect) > 0)
                {
                    $sa=join(",", $aIndirect);
                    $res.="<script type='text/javascript'>
                aIndirectWall=[{$sa}];
                </script>
                ";
                    $was=true;
                }
            
            }
            
            global $sCWCurContainerType, $iCWCurContainerId, $sWallScriptData;
            
            $sWallScriptData=$res;
            
            $sCWCurContainerType='lswall';
            $iCWCurContainerId=$this->oUserProfile->getId();
            
            $aClear=array_merge($aDirect, $aIndirect,array(0));
            
            $aActAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActiveAndNcommentIdIn($oUserCurrent->getId(), 'lswall', $this->oUserProfile->getId(), array('lswall_direct', 'lswall'), 1, $aClear);
            foreach ($aActAnswer as $oActAnswer)
                $oActAnswer->delete();
            $aActAnswer=$this->PluginCWCore_Watcher_GetDataItemsByOwnerIdAndContainerTypeAndContainerIdAndCommentTypeInAndCommentActiveAndNcommentedIdIn($oUserCurrent->getId(), 'lswall', $this->oUserProfile->getId(), array('lswall_direct', 'lswall'), 1, $aClear);
            foreach ($aActAnswer as $oActAnswer)
                $oActAnswer->delete();
        }
        
        return $aResult;
    }
}
?>