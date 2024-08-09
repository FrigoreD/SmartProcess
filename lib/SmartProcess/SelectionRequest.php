<?php

namespace Agapov\Main\SmartProcess;

use Agapov\Main\Helper\SmartProcessDataManager;
use Bitrix\Main\Type\DateTime;

/**
 * Класс для работы со стадиями смарт-процесса
 */
class SelectionRequest
{
    private SmartProcessDataManager $smartProcessDataManager;

    private int $entityId;

    /**
     * @param int $entityId
     */
    public function __construct(int $entityId)
    {
        $this->entityId = $entityId;
        $this->smartProcessDataManager = new SmartProcessDataManager(SELECTION_REQUEST_ENTITY_TYPE_ID);
    }

    /**
     * @return SmartProcessDataManager
     */
    public function getSmartProcessDataManager(): SmartProcessDataManager
    {
        return $this->smartProcessDataManager;
    }

    /**
     * @param string $stage
     * @param array $arAdditionalFieldsToUpdate
     * @return bool
     */
    public function setProcessStage(string $stage, array $arAdditionalFieldsToUpdate = []): bool
    {
        $fieldsToUpdate = array_merge(['STAGE_ID' => $this->prepareStageString($stage)], $arAdditionalFieldsToUpdate);
        return $this
            ->getSmartProcessDataManager()
            ->update($this->entityId, $fieldsToUpdate)
            ->isSuccess()
            ;
    }

    /**
     * @return bool
     */
    public function setRejectedStage(): bool
    {
        return $this->setProcessStage('REJECT');
    }

    /**
     * @return bool
     */
    public function setCanceledStage(): bool
    {
        return $this->setProcessStage('CANCELED', ['UF_CANCEL_DATE' => new DateTime()]);
    }

    /**
     * @param string $stage
     * @return string
     */
    private function prepareStageString(string $stage): string
    {
        return 'DT' . SELECTION_REQUEST_ENTITY_TYPE_ID . '_' . SELECTION_REQUEST_ENTITY_ID . ':' . $stage;
    }
}
