<?php

namespace Sprint\Migration;

use Bitrix\Crm\PhaseSemantics;
use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\LoaderException;
use Agapov\Main\Helper\SmartProcessMigration;
use Throwable;
use Bitrix\Main\Loader;


class Smart_process_20240809120001 extends Version
{
    const ENTITY_NAME = 'Заявка на подбор';
    const ENTITY_CODE = 'Q_SMART_SELECTION_REQUEST';
    const ENTITY_SHORT_CODE = 'SELECTION_REQUEST';

    protected $description = 'Создает смарт-процесс Заявка на подбор';

    protected $moduleVersion = '4.1.1';

    protected SmartProcessMigration $migration;

    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::includeModule('crm');
        Loader::includeModule('agapov.smart_process');

        $this->migration = new SmartProcessMigration(
            self::ENTITY_NAME,
            self::ENTITY_CODE,
            self::ENTITY_SHORT_CODE
        );
    }

    /**
     * @return bool
     * @throws SqlQueryException
     */
    public function up(): bool
    {
        $connection = Application::getConnection();
        try {
            $connection->startTransaction();

            $settings = [
                'isBizProcEnabled' => true,
                'isStagesEnabled' => true,
            ];

            $this->migration->createProcess($settings);

            $stagesSettings = [
                [
                    'NAME' => 'Черновик',
                    'STATUS_ID' => 'NEW',
                    'COLOR' => '#90EE90',
                    'SYSTEM' => 'Y',
                    'SORT' => 10,
                ],
                [
                    'NAME' => 'На согласовании',
                    'STATUS_ID' => 'AGREEMENT',
                    'COLOR' => '#6495ED',
                    'SYSTEM' => 'Y',
                    'SORT' => 20,
                ],
                [
                    'NAME' => 'На доработке',
                    'STATUS_ID' => 'REWORK',
                    'COLOR' => '#FFE4B5',
                    'SYSTEM' => 'Y',
                    'SORT' => 30,
                ],
                [
                    'NAME' => 'Отклонено',
                    'STATUS_ID' => 'REJECT',
                    'COLOR' => '#DCDCDC',
                    'SYSTEM' => 'Y',
                    'SORT' => 40,
                    'SEMANTICS' => PhaseSemantics::FAILURE,
                ],
                [
                    'NAME' => 'Согласовано',
                    'STATUS_ID' => 'SUCCESS',
                    'COLOR' => '#32CD32',
                    'SYSTEM' => 'Y',
                    'SORT' => 50,
                ],
                [
                    'NAME' => 'Верификация',
                    'STATUS_ID' => 'VERIFICATION',
                    'COLOR' => '#C71585',
                    'SYSTEM' => 'Y',
                    'SORT' => 60,
                ],
                [
                    'NAME' => 'Не подтверждено',
                    'STATUS_ID' => 'NOT_CONFIRMED',
                    'COLOR' => '#BC8F8F',
                    'SYSTEM' => 'Y',
                    'SORT' => 70,
                ],
                [
                    'NAME' => 'Подтверждено',
                    'STATUS_ID' => 'CONFIRMED',
                    'COLOR' => '#008000',
                    'SYSTEM' => 'Y',
                    'SORT' => 80,
                ],
                [
                    'NAME' => 'В работе',
                    'STATUS_ID' => 'IN_PROCESS',
                    'COLOR' => '#1E90FF',
                    'SYSTEM' => 'Y',
                    'SORT' => 90,
                ],
                [
                    'NAME' => 'Выполнено',
                    'STATUS_ID' => 'DONE',
                    'COLOR' => '#4682B4',
                    'SYSTEM' => 'Y',
                    'SORT' => 100,
                    'SEMANTICS' => PhaseSemantics::SUCCESS,
                ],
                [
                    'NAME' => 'Отменено',
                    'STATUS_ID' => 'CANCELED',
                    'COLOR' => '#DC143C',
                    'SYSTEM' => 'Y',
                    'SORT' => 110,
                    'SEMANTICS' => PhaseSemantics::FAILURE,
                ],
            ];

            $this->migration->setStages($stagesSettings);

            $this->outSuccess('Добавлена CRM-сущность ' . self::ENTITY_CODE);

            $connection->commitTransaction();

            return true;

        } catch (Throwable $exception) {
            $connection->rollbackTransaction();

            $this->outError('Ошибка: ' . $exception->getMessage());

            return false;
        }
    }


    public function down()
    {

    }
}
