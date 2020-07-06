<?php

use ConsoleKit\Console,
    ConsoleKit\Command,
    ConsoleKit\Colors,
    ConsoleKit\Utils,
    ConsoleKit\Widgets\Dialog,
    ConsoleKit\Widgets\ProgressBar;

/**
 * Migration commands
 */
class LsConsoleCommandMigration extends LsConsoleCommandBase
{
    public function executeCreate(array $args, array $options = array())
    {
        if (!($sName = Utils::get($args, 0))) {
            $this->writeerr('The name migration is not specified.')->writeln('');
            return;
        }
        $sPlugin = Utils::get($options, 'plugin');
        $sNameUnderscore = func_underscore($sName);
        if ($sPlugin) {
            $sPlugin = func_underscore($sPlugin);
            $sPath = Config::Get('path.application.plugins.server') . DIRECTORY_SEPARATOR . $sPlugin . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        } else {
            $sPath = Config::Get('path.root.server') . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;
        }

        $sDirParent = dirname($sPath);
        if (!is_dir($sDirParent)) {
            $this->writeerr("The directory '{$sDirParent}' is not valid.")->writeln('');
            return;
        }
        if (!is_dir($sPath)) {
            @mkdir($sPath, 0755, true);
        }
        if (!is_dir($sPath)) {
            $this->writeerr("Can not create 'migrations' directory.")->writeln('');
            return;
        }

        $sVersion = (string)time();
        $sFileName = $sVersion . '_' . $sNameUnderscore . '.php';
        $sClassName = 'Migration_' . $sVersion;
        if ($sPlugin) {
            $sClassName = 'Plugin' . func_camelize($sPlugin) . '_' . $sClassName;
        }
        $sSourceFile = dirname(__DIR__) . '/protected/migration/migration.php';
        $this->copyFiles(array(
            array(
                'source'   => $sSourceFile,
                'target'   => $sPath . $sFileName,
                'callback' => function ($sSource, $aParams) use ($sClassName) {
                    $sContent = file_get_contents($sSource);
                    $sContent = str_replace('{{className}}', $sClassName, $sContent);
                    return $sContent;
                }
            )
        ));

        $this->writeln("Your migration has been created successfully: " . $sPath . $sFileName, Colors::GREEN);
    }

    public function executeMigrate(array $args, array $options = array())
    {
        $bIsDry = Utils::get($options, 'dry', false);
        $sPlugin = Utils::get($options, 'plugin');
        /**
         * Список каталогов, где могут быть миграции
         */
        $aLocations = Engine::getInstance()->Migration_GetLocationsForMigration($sPlugin);
        if (!$aLocations) {
            $this->writeln("Not found migrate locations", Colors::GREEN);
            return;
        }
        $this->writeln("Found migrate locations:", Colors::GREEN);
        foreach ($aLocations as $aLocation) {
            $this->writeln($aLocation['path'], Colors::YELLOW);
        }

        $aMigrationCompleted = array();
        foreach ($aLocations as $aLocation) {
            $aMigrationFiles = Engine::getInstance()->Migration_GetNewMigrationFiles($aLocation);
            $iCountNew = count($aMigrationFiles);
            $this->writeln("Location {$aLocation['path']}: " . $iCountNew . ' new migrations', Colors::GREEN);
            if ($aMigrationFiles) {
                foreach ($aMigrationFiles as $sMigrationFile) {
                    $iTime1 = microtime(true);
                    $this->writeln("Migrate {$sMigrationFile}", Colors::YELLOW);
                    $bRes = true;
                    if ($bIsDry) {
                        sleep(1);
                    } else {
                        $bRes = Engine::getInstance()->Migration_RunMigrationUp($aLocation, $sMigrationFile);
                    }
                    if ($bRes) {
                        $iTime2 = microtime(true);
                        $fSeconds = round(($iTime2 - $iTime1), 2);
                        $this->writeln("Migrated {$sMigrationFile}: {$fSeconds} sec", Colors::YELLOW);

                        $aMigrationCompleted[] = array(
                            'location' => $aLocation,
                            'file'     => $sMigrationFile
                        );
                    } else {
                        /**
                         * Откатываем все обратно
                         */
                        if ($aMigrationCompleted) {
                            $i = count($aMigrationCompleted);
                            while ($i) {
                                $aData = $aMigrationCompleted[--$i];
                                Engine::getInstance()->Migration_RunMigrationDown($aData['location'], $aData['file']);
                            }
                        }

                        $this->writeln("Failed", Colors::RED);
                        exit(1);
                    }
                }
            }
        }

        $this->writeln("All migrate successfully", Colors::GREEN);
    }
}