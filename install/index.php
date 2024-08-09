<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Sprint\Migration;

class agapov_smart_process extends CModule
{
    public $MODULE_ID = 'agapov.smart_process';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    protected $migrationsPath = __DIR__ . '/migrations/';
    protected $bitrixMigrationsPath = '/local/php_interface/migrations/';

    private bool $hasMigrationModule;
    private array $migrations = [];

    function __construct()
    {
        $arModuleVersion = [];

        $this->MODULE_PATH = $this->getModulePath();

        include $this->MODULE_PATH . '/install/version.php';
        include $this->MODULE_PATH . "/include.php";

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('AGAPOV_SMART_PROCESS_MODULE_NAME');
        $this->MODULE_DESCRIPTION = '';

        $this->PARTNER_NAME = 'AGAPOV';
        $this->PARTNER_URI = 'https://agapov.ru/';

        $this->hasMigrationModule = Loader::includeModule('sprint.migration');
        $this->bitrixMigrationsPath = $_SERVER['DOCUMENT_ROOT'] . $this->bitrixMigrationsPath;

        $this->migrations = $this->getMigrations();
    }

    /**
     * @return bool
     */
    function DoInstall()
    {
        global $APPLICATION;
        $APPLICATION->ResetException();

        $this->migrate();

        ModuleManager::registerModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage('AGAPOV_SMART_PROCESS_MODULE_INSTALL_DO'), $this->MODULE_PATH . '/install/step.php');

    }

    function DoUninstall()
    {
        global $APPLICATION;
        $APPLICATION->ResetException();

        $this->unmigrate();

        ModuleManager::unregisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage('AGAPOV_SMART_PROCESS_MODULE_UNINSTALL_DO'), $this->MODULE_PATH . '/install/unstep.php');

    }

    /**
     * @throws Exception
     */
    protected function migrate(): void
    {
        if ($this->hasMigrationModule) {
            $this->moveMigrations();
            $this->startMigration('up');
        } else {
            throw new Exception('Не установлен модуль sprint.migration');
        }
    }

    protected function moveMigrations(): void
    {
        foreach ($this->migrations as $migration) {
            if (is_dir($this->migrationsPath . $migration)) {
                $this->copyDirectory($this->migrationsPath . $migration, $this->bitrixMigrationsPath . $migration);
            }

            copy($this->migrationsPath . $migration, $this->bitrixMigrationsPath . $migration);
        }
    }

    protected function copyDirectory(string $from, string $to): void
    {
        if (!mkdir($to) && !is_dir($to)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $to));
        }

        foreach ($this->getAllFiles($from) as $file) {
            $fromFile = $from . '/' . $file;
            $toFile = $to . '/' . $file;

            if (is_dir($file)) {
                $this->copyDirectory($fromFile, $toFile);
            } else {
                copy($fromFile, $toFile);
            }
        }
    }

    protected function startMigration(string $action): void
    {
        global $APPLICATION;

        $notInstalled = [];
        $versionConfig = new Migration\VersionConfig();
        $versionManager = new Migration\VersionManager($versionConfig);

        foreach ($this->migrations as $versionName) {
            $versionName = $this->getFilename($versionName);
            $result = $versionManager->startMigration($versionName, $action);

            if ($result === false) {
                $notInstalled[] = $versionName;
            }
        }

        foreach ($notInstalled as $versionName) {
            $versionName = $this->getFilename($versionName);
            $versionManager->startMigration($versionName, $action);
        }

        $APPLICATION->RestartBuffer();
    }

    protected function getFilename(string $fullFilename): string
    {
        return str_replace('.php', '', $fullFilename);
    }

    protected function getAllFiles(string $path): array
    {
        $directory = scandir($path);

        $directory = is_array($directory) ? $directory : [];

        $result = array_diff($directory, ['..', '.']);

        return is_array($result) ? $result : [];
    }

    /**
     * @throws Exception
     */
    protected function unmigrate(): void
    {
        if ($this->hasMigrationModule) {
            $this->startMigration('down');
            $this->removeMigrations();
            $this->removeConstFile();
        } else {
            throw new Exception('Не установлен модуль sprint.migration');
        }
    }

    protected function getMigrations(): array
    {
        foreach ($this->getAllFiles($this->migrationsPath) as $migration) {
            $result[] = $migration;
        }

        return $result ?? [];
    }

    private function removeMigrations(): void
    {
        foreach ($this->migrations as $migration) {
            $path = $this->bitrixMigrationsPath . $migration;

            if (is_dir($path)) {
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }

    private function removeConstFile(): void
    {
        $constPath = __DIR__ . '/../const.php';
        unlink($constPath);
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath()
    {
        $modulePath = explode('/', __FILE__);
        $modulePath = array_slice(
            $modulePath,
            0,
            array_search($this->MODULE_ID, $modulePath) + 1
        );

        return join('/', $modulePath);
    }
}
