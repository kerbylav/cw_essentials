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

    public function GetUsersBySubscribe($sTargetId,$sTargetType)
    {
        $sql="
              SELECT u.user_id user_id FROM " . Config::Get('db.table.subscribe') . " s inner join " . Config::Get('db.table.user') . " u on s.mail=u.user_mail
              WHERE
					target_id = ?
				AND
					status = 1
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