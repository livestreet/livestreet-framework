<?php

class ModuleMigration extends ModuleORM
{
    const STATE_UP = 1;
    const STATE_DOWN = 0;

    public function GetLocationsForMigration($sPlugin = null)
    {
        $aLocations = array();
        if ($sPlugin) {
            $sPlugin = func_underscore($sPlugin);
            $aLocations[] = array(
                'path'   => Config::Get('path.application.plugins.server') . DIRECTORY_SEPARATOR . $sPlugin . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
                'plugin' => $sPlugin
            );
        } else {
            /**
             * Основные миграции
             */
            $aLocations[] = array(
                'path'   => Config::Get('path.root.server') . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
                'plugin' => null
            );
            $aPlugins = func_list_plugins();
            foreach ($aPlugins as $sPlugin) {
                $aLocations[] = array(
                    'path'   => Config::Get('path.application.plugins.server') . DIRECTORY_SEPARATOR . $sPlugin . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR,
                    'plugin' => $sPlugin,
                );
            }
        }
        foreach ($aLocations as $i => $aLocation) {
            if (!is_dir($aLocation['path'])) {
                unset($aLocations[$i]);
            }
        }
        return $aLocations;
    }

    public function GetLastMigration($sPlugin = null)
    {
        $oMigrationLast = $this->GetMigrationByFilter(array(
            'plugin' => $sPlugin,
            'state'  => self::STATE_UP,
            '#order' => array('version' => 'desc')
        ));
        return $oMigrationLast;
    }

    public function GetLastMigrationVersion($sPlugin = null)
    {
        if ($oMigrationLast = $this->GetLastMigration($sPlugin)) {
            return (int)$oMigrationLast->getVersion();
        }
        return null;
    }

    public function GetNewMigrationFiles($aLocation)
    {
        $aMigrationFiles = array();
        $aFiles = glob($aLocation['path'] . "*.php");
        if ($aFiles) {
            $iVersionLast = $this->GetLastMigrationVersion($aLocation['plugin']);
            foreach ($aFiles as $sFile) {
                if (!$iVersionLast or ($this->GetVersionFromFileName($sFile) > $iVersionLast)) {
                    $aMigrationFiles[] = basename($sFile);
                }
            }
        }
        return $aMigrationFiles;
    }

    public function GetVersionFromFileName($sFile)
    {
        $sFile = basename($sFile);
        if (preg_match('#^(\d+)\_(\w+)#', $sFile, $aMatch)) {
            return (int)$aMatch[1];
        }
        return null;
    }

    public function RunMigrationUp($aLocation, $sMigrationFile)
    {
        $sFile = $aLocation['path'] . $sMigrationFile;
        if (!file_exists($sFile)) {
            return false;
        }
        if ($sVersion = $this->GetVersionFromFileName($sMigrationFile)) {
            $sClass = 'Migration_' . $sVersion;
            if ($aLocation['plugin']) {
                $sClass = 'Plugin' . func_camelize($aLocation['plugin']) . '_' . $sClass;
            }
            require_once($sFile);
            if (class_exists($sClass)) {
                $oMigration = new $sClass();
                $oMigration->up();
                $oMigrationVersion = Engine::GetEntity('ModuleMigration_EntityMigration');
                $oMigrationVersion->setPlugin($aLocation['plugin']);
                $oMigrationVersion->setVersion($sVersion);
                $oMigrationVersion->setState(self::STATE_UP);
                $oMigrationVersion->add();
                return true;
            }
        }
        return false;
    }

    public function RunMigrationDown($aLocation, $sMigrationFile)
    {
        $sFile = $aLocation['path'] . $sMigrationFile;
        if (!file_exists($sFile)) {
            return false;
        }
        if ($sVersion = $this->GetVersionFromFileName($sMigrationFile)) {
            if ($oMigrationVersion = $this->Migration_GetMigrationByFilter(array(
                'version' => $sVersion,
                'plugin'  => $aLocation['plugin'],
                'state'   => self::STATE_UP
            ))) {

                $sClass = 'Migration_' . $sVersion;
                if ($aLocation['plugin']) {
                    $sClass = 'Plugin' . func_camelize($aLocation['plugin']) . '_' . $sClass;
                }
                require_once($sFile);
                if (class_exists($sClass)) {
                    $oMigration = new $sClass();
                    $oMigration->down();
                    $oMigrationVersion->setState(self::STATE_DOWN);
                    $oMigrationVersion->update();
                    return true;
                }
            }
        }
        return false;
    }
}