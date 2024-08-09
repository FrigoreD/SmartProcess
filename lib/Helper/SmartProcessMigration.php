<?php

namespace Agapov\Main\Helper;

use Bitrix\Crm\Controller\Type;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\IO\File;
use Bitrix\Main\NotSupportedException;
use Exception;
use CCrmStatus;

/**
 * Класс хелпер для создания смарт-процессов
 */
class SmartProcessMigration
{
    const CONSTANT_FILE_PATH = __DIR__ . '/../../const.php';

    protected string $entityName;
    protected string $entityCode;
    protected string $shortEntityCode;

    protected int $entityId;
    protected int $categoryId;
    protected int $entityTypeId;

    protected File $file;

    public function __construct($entityName, $entityCode, $shortEntityCode)
    {
        $this->entityName = $entityName;
        $this->entityCode = $entityCode;
        $this->shortEntityCode = $shortEntityCode;
    }

    /**
     * @param array $settings
     * @return void
     * @throws Exception
     */
    public function createProcess(array $settings = []): void
    {
        $this->createConstantsFile();

        $processFields = $this->getProcessSettings($settings);

        $newProcess = (new Type())->addAction($processFields);

        $this->entityId = $newProcess['type']['id'];
        $this->entityTypeId = $newProcess['type']['entityTypeId'];

        $factory = Container::getInstance()->getFactory($this->entityTypeId);
        $this->categoryId = $factory->getDefaultCategory()->getId();

        $this->writeConstantsToFile();
    }

    /**
     * @param array $settings
     * @return array
     */
    private function getProcessSettings(array $settings = []): array
    {
        global $USER;

        $defaultSettings = [
            'title' => $this->entityName,
            'createdBy' => $USER->GetID(),
            'code' => $this->entityCode,
            'isCategoriesEnabled' => false,
            'isStagesEnabled' => false,
            'isBeginCloseDatesEnabled' => false,
            'isClientEnabled' => false,
            'isUseInUserfieldEnabled' => false, // Позволяет создавать пользовательские поля с привязкой к этому Смарт-процессу
            'isLinkWithProductsEnabled' => false,
            'isMycompanyEnabled' => false,
            'isDocumentsEnabled' => false,
            'isSourceEnabled' => false,
            'isObserversEnabled' => false,
            'isRecyclebinEnabled' => false,
            'isAutomationEnabled' => false,
            'isBizProcEnabled' => false,
            'isSetOpenPermissions' => false,
            'isPaymentsEnabled' => false,
            'linkedUserFields' => [
                'CALENDAR_EVENT|UF_CRM_CAL_EVENT' => 'false',
                'TASKS_TASK|UF_CRM_TASK' => 'false',
                'TASKS_TASK_TEMPLATE|UF_CRM_TASK' => 'false',
            ],
        ];

        $resultSettings = [];
        foreach ($defaultSettings as $key => $setting) {
            $resultSettings[$key] = $settings[$key] ?? $setting;
        }

        return $resultSettings;
    }

    /**
     * @return void
     */
    private function writeConstantsToFile(): void
    {
        $constants = [
            $this->shortEntityCode . '_ENTITY_ID' => $this->entityId,
            $this->shortEntityCode . '_ENTITY_TYPE_ID' => $this->entityTypeId,
            $this->shortEntityCode . '_CATEGORY_ID' => $this->categoryId,
        ];

        $this->file->putContents("// {$this->entityName}\n", File::APPEND);
        foreach ($constants as $constantName => $constantValue) {
            $this->file->putContents("const $constantName = $constantValue;\n", File::APPEND);
        }
        $this->file->putContents("\n", File::APPEND);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function createConstantsFile(): void
    {
        $filePath = self::CONSTANT_FILE_PATH;
        $this->file = new File($filePath);

        $ok = true;
        if ($this->file->isExists()) {
            if(!is_writable($filePath)){
                $ok = false;
            }
        } else {
            $ok = $this->file->putContents("<?php" . "\n");
        }

        if(!$ok){
            throw new Exception('Не удалось открыть на запись файл');
        }
    }

    /**
     * @param array $stagesSettings
     * @return void
     * @throws NotSupportedException
     */
    public function setStages(array $stagesSettings): void
    {
        $categoryId = $this->categoryId;
        $entityTypeId = $this->entityTypeId;

        $this->entityStatus = 'DT' . $entityTypeId . '_' . $categoryId . ':';
        $entityStatusId = 'DYNAMIC_' . $entityTypeId . '_STAGE_' . $categoryId;

        $this->entity = new CCrmStatus($entityStatusId);

        CCrmStatus::Erase($entityStatusId);

        foreach ($stagesSettings as $stageSetting) {
            $stageSetting['CATEGORY_ID'] = $categoryId;
            $this->addStage($stageSetting);
        }
    }

    /**
     * @param array $stageSettings
     * @return void
     * @throws NotSupportedException
     */
    private function addStage(array $stageSettings): void
    {
        $stageSettings['STATUS_ID'] = $this->entityStatus . $stageSettings['STATUS_ID'];

        if (!$this->entity->CheckStatusId($stageSettings['STATUS_ID'])) {
            $this->entity->Add($stageSettings);
        }
    }
}
