<?php

use ConsoleKit\Console,
    ConsoleKit\Command,
    ConsoleKit\Colors,
    ConsoleKit\Utils,
    ConsoleKit\Widgets\Dialog,
    ConsoleKit\Widgets\ProgressBar;

/**
 * Plugin commands
 */
class LsConsoleCommandPlugin extends LsConsoleCommandBase
{
    protected $_name;
    protected $_name_under;

    /**
     * Generate new empty plugin
     *
     * @arg code The code of the new plugin. Use only [a-z_] symbols.
     */
    public function executeNew(array $args, array $options = array())
    {
        if (!($code = Utils::get($args, 0))) {
            $this->writeerr('The plugin code is not specified.')->writeln('');
            return;
        }

        $this->_name_under = func_underscore($code);
        $this->_name = func_camelize($this->_name_under);

        $path = strtr($this->_name_under, '/\\', DIRECTORY_SEPARATOR);
        $path = Config::Get('path.application.plugins.server') . '/' . $path;
        if (strpos($path, DIRECTORY_SEPARATOR) === false) {
            $path = '.' . DIRECTORY_SEPARATOR . $path;
        }

        $dir = rtrim(realpath(dirname($path)), '\\/');
        if ($dir === false || !is_dir($dir)) {
            $this->writeerr("The directory '{$path}' is not valid. Please make sure the parent directory exists.")->writeln('');
            return;
        }

        $sourceDir = dirname(__DIR__) . '/protected/plugin';
        if ($sourceDir === false) {
            $this->writeerr('Unable to locate the source directory.')->writeln('');
            return;
        }

        // Создаем массив файлов для функции копирования
        $aList = $this->buildFileList($sourceDir, $path);

        // Парсим имена плагинов и пересоздаем массив
        foreach ($aList as $sName => $aFile) {
            $sTarget = str_replace('Example', $this->_name, $aFile['target']);
            $sTarget = str_replace('example', $this->_name_under, $sTarget);
            $sNewName = str_replace('Example', $this->_name, $sName);
            $sNewName = str_replace('example', $this->_name_under, $sNewName);
            if ($sName != $sNewName) {
                unset($aList[$sName]);
            }

            $aFile['target'] = $sTarget;
            $aList[$sNewName] = $aFile;
            $aList[$sNewName]['callback'] = array($this, 'generatePlugin');
        }

        // Копируем файлы
        $this->copyFiles($aList);

        $this->writeln("Your plugin has been created successfully under {$path}", Colors::GREEN);
    }

    /*
     * Парсер выражений в исходниках эталонного плагина
     */
    public function generatePlugin($source, $params)
    {
        $content = file_get_contents($source);
        $content = str_replace('Example', $this->_name, $content);
        $content = str_replace('example', $this->_name_under, $content);
        return $content;
    }
}