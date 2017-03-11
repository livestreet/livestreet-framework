<?php

use ConsoleKit\Console,
    ConsoleKit\Command,
    ConsoleKit\Colors,
    ConsoleKit\Utils,
    ConsoleKit\Widgets\Dialog,
    ConsoleKit\Widgets\ProgressBar;

/**
 * Base class for commands
 */
abstract class LsConsoleCommandBase extends Command
{
    /** @var array */
    protected $defaultFormatOptions = array();

    /*
     * Создает массив файлов используемый при копировании
     */
    public function buildFileList($sSourceDir, $sTargetDir, $sBaseDir = '')
    {
        $aList = array();
        $handle = opendir($sSourceDir);
        while (($sFile = readdir($handle)) !== false) {
            if ($sFile === '.' || $sFile === '..' || $sFile === '.svn' || $sFile === '.gitignore') {
                continue;
            }

            $sSourcePath = $sSourceDir . DIRECTORY_SEPARATOR . $sFile;
            $sTargetPath = $sTargetDir . DIRECTORY_SEPARATOR . $sFile;

            $sName = ($sBaseDir === '') ? $sFile : $sBaseDir . '/' . $sFile;

            // Строим массив с ключем в виде имени файла или папки, пути к исходнику и пути назначения
            $aList[$sName] = array(
                'source' => $sSourcePath,
                'target' => $sTargetPath
            );

            // Если директория то рекурсивно получаем массив его содержимого и объединяем с главным
            if (is_dir($sSourcePath)) {
                $aList = array_merge($aList, $this->buildFileList($sSourcePath, $sTargetPath, $sName));
            }
        }
        closedir($handle);
        return $aList;
    }

    /*
     * Копирование файлов
     */
    public function copyFiles($fileList)
    {
        $progress = new ProgressBar($this->console, count($fileList));
        $overwriteAll = false;
        foreach ($fileList as $name => $file) {
            $progress->incr();
            $source = strtr($file['source'], '/\\', DIRECTORY_SEPARATOR);
            $target = strtr($file['target'], '/\\', DIRECTORY_SEPARATOR);

            $callback = isset($file['callback']) ? $file['callback'] : null;
            $params = isset($file['params']) ? $file['params'] : null;

            if (is_dir($source)) {
                // Проверяем существует ли директория или досоздаем папки
                $this->ensureDirectory($target);
                continue;
            }

            // Если существует коллбэк то вызываем его
            if ($callback !== null) {
                $content = call_user_func($callback, $source, $params);
            } // Либо отдаем содержимое исходника без изменений
            else {
                $content = file_get_contents($source);
            }

            // Если файл в папке назначения уже существует
            if (is_file($target)) {
                // Если содержимое старого и нового файла совпадают
                if ($content === file_get_contents($target)) {
                    $this->writeln("  unchanged $name");
                    continue;
                }

                // Если мы выбрали перезапись в ветке false
                if ($overwriteAll) {
                    $this->writeln("  overwrite $name", Colors::BLUE);
                } else {
                    $this->writeln("      exist $name");
                    $this->writeln("            ...overwrite? [Yes|No|All|Quit] ");

                    $dialog = new Dialog($this->console);
                    $answer = $dialog->ask('            ...overwrite? [Yes|No|All|Quit] ', 'No');
                    if (!strncasecmp($answer, 'q', 1)) {
                        $this->writeerr('Copy files break')->writeln('');
                        return;
                    } else {
                        if (!strncasecmp($answer, 'y', 1)) {
                            $this->writeln("  overwrite $name", Colors::BLUE);
                        } else {
                            if (!strncasecmp($answer, 'a', 1)) {
                                $this->writeln("  overwrite $name", Colors::BLUE);
                                $overwriteAll = true;
                            } else {
                                $this->writeln("       skip $name");
                                continue;
                            }
                        }
                    }
                }
            } // Если файла еще не существует
            else {
                // Досоздаем папки в случае отсутствия
                $this->ensureDirectory(dirname($target));
                $this->writeln("   generate $name");
            }

            // Создаем файл и записываем в него содержимое
            file_put_contents($target, $content);
        }
        $progress->stop();
    }

    /**
     * Создает родительские папки если они не существуют
     * @param string $directory
     */
    public function ensureDirectory($directory)
    {
        if (!is_dir($directory)) {
            $this->ensureDirectory(dirname($directory));
            $this->writeln("      mkdir " . strtr($directory, '\\', '/'));
            mkdir($directory);
        }
    }
}