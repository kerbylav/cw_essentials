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

class PluginCWCore_ModuleWatcher_MapperWatcher extends MapperORM
{

    public function GetUsersByFavority($sTargetId,$sTargetType)
    {
        $sql="			
			SELECT user_id										
			FROM " . Config::Get('db.table.favourite') . "								
			WHERE 
					target_id = ?
				AND
					target_publish = 1
				AND
					target_type = ? 
";
        $aUsers=array();
        if ($aRows=$this->oDb->select($sql, $sTargetId, $sTargetType))
        
        {
            foreach ($aRows as $aRow)
            {
                $aUsers[]=$aRow['user_id'];
            }
        }
        return $aUsers;
    }
    
    public function GetGroupedData($iUserId,$aTypes,$aActType,&$iCount,$iPage,$iPerPage)
    {
        if (!is_array($aTypes)) $aTypes=array($aTypes);
        if (!is_array($aActType)) $aActType=array($aActType);
        if (count($aTypes)==0) return array();
        
        $sql="
        SELECT container_type, container_id FROM ". MapperORM::GetTableName(Engine::GetEntity('PluginCWCore_ModuleWatcher_EntityData')) . " where owner_id=?d and container_type in (?a) and comment_type in (?a) and comment_active=1 group by container_type, container_id order by date_add			
LIMIT ?d , ?d ;";
        $aData=array();
        if ($aRows=$this->oDb->selectPage($iCount,$sql, $iUserId, $aTypes,$aActType,($iPage-1)*$iPerPage, $iPerPage))
        {
            foreach ($aRows as $aRow)
            {
                $aData[]=array('type'=>$aRow['container_type'],'id'=>$aRow['container_id']);
            }
        }
        return $aData;
    }
    
}
?>