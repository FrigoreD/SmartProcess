<?php

namespace Agapov\Main\Helper;

use Bitrix\Crm\Service\Container;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Entity\UpdateResult;
use Bitrix\Main\ORM\Query\Result;

/**
 * Класс для работы с таблицами смарт-процессов
 */
class SmartProcessDataManager
{
    private int $dataTypeId;

    private string $dataClass;

    /**
     * @param int $dataTypeId
     * @throws LoaderException
     */
    public function __construct(int $dataTypeId)
    {
        Loader::requireModule('crm');
        $this->dataTypeId = $dataTypeId;
        $this->dataClass = Container::getInstance()->getFactory($this->dataTypeId)->getDataClass();
    }

    /**
     * @return string
     */
    public function getDataClass(): string
    {
        return $this->dataClass;
    }

    /**
     * @param int $id
     * @param array $fields
     * @return UpdateResult
     */
    public function update(int $id, array $fields): UpdateResult
    {
        return $this->getDataClass()::update($id, $fields);
    }

    /**
     * @param int $id
     * @return Result
     */
    public function getById(int $id): Result
    {
        return $this->getDataClass()::getById($id);
    }
}
