<?php

class ModuleMigration_EntityMigration extends EntityORM
{
    protected function beforeSave()
    {
        if ($bResult = parent::beforeSave()) {
            $this->setDateCreate(date("Y-m-d H:i:s"));
        }
        return $bResult;
    }
}