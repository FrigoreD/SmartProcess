<?php

namespace Sprint\Migration;

use Bitrix\Main\Application;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\Loader;
use CUserOptions;
use Exception;
use Throwable;

Loader::includeModule('agapov.smart_process');

class Smart_process_20240809120003 extends Version
{
    protected $description = 'Добавляет настройки отображения полей смарт-процесса "Заявка на подбор"';

    protected $moduleVersion = '4.1.1';

    const CATEGORY = 'crm.entity.editor';
    const NAME = 'DYNAMIC_' . SELECTION_REQUEST_ENTITY_TYPE_ID . '_details_C' . SELECTION_REQUEST_CATEGORY_ID . '_common';
    const IS_COMMON = 'Y';
    const USER_ID = 0;

    /**
     * @return bool
     * @throws SqlQueryException
     */
    public function up(): bool
    {
        $connection = Application::getConnection();
        try {
            $connection->startTransaction();

            $userOptionsSetResult = CUserOptions::SetOption(
                self::CATEGORY,
                self::NAME,
                $this->getViewParams(),
                self::IS_COMMON,
                self::USER_ID
            );

            if (!$userOptionsSetResult) {
                throw new Exception('При установке миграции возникла ошибка');
            }

            $this->outSuccess('Установка миграции успешно проведена');

            $connection->commitTransaction();

            return true;

        } catch (Throwable $exception) {
            $connection->rollbackTransaction();
            $this->outError('Ошибка ' . $exception->getMessage());

            return false;
        }
    }

    /**
     * @return bool
     * @throws SqlQueryException
     */
    public function down(): bool
    {
        $connection = Application::getConnection();
        try {
            $connection->startTransaction();

            $userOptionsDeleteResult = CUserOptions::DeleteOption(
                self::CATEGORY,
                self::NAME,
                self::IS_COMMON,
                self::USER_ID
            );

            if (!$userOptionsDeleteResult) {
                throw new Exception('При откате миграции возникла ошибка');
            }

            $this->outSuccess('Откат миграции успешно проведен');

            $connection->commitTransaction();

            return true;

        } catch (Throwable $exception) {
            $connection->rollbackTransaction();
            $this->outError('Ошибка ' . $exception->getMessage());

            return false;
        }
    }

    /**
     * @return array[]
     */
    protected function getViewParams(): array
    {
        return [
            [
                'name' => 'default_column',
                'type' => 'column',
                'elements' => [
                    [
                        'name' => 'initiator_block',
                        'title' => 'Инициатор заявки на подбор',
                        'type' => 'section',
                        'elements' => [
                            [
                                'name' => 'CREATED_BY',
                                'title' => 'Кем создан',
                                'optionFlags' => '1',
                            ],
                        ],
                    ],
                    [
                        'name' => 'main',
                        'title' => 'Заявка на подбор',
                        'type' => 'section',
                        'elements' => [
                            [
                                'name' => 'ID',
                                'title' => 'Номер',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'CREATED_TIME',
                                'title' => 'Дата создания',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_END_DATE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_CANCEL_DATE',
                                'optionFlags' => '1',
                            ],
                        ],
                    ],
                    [
                        'name' => 'executor_block',
                        'title' => 'Исполнитель заявки на подбор',
                        'type' => 'section',
                        'elements' => [
                            [
                                'name' => 'ASSIGNED_BY_ID',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_POST_NAME',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_NEW_POST',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_MASS_SELECTION',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_VACANCY_CATEGORY',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_VACANCY_REASON',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_DIRECT_SUBORDINATION',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_SUBORDINATES_COUNT',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_FUNCTIONAL_RESPONSIBILITIES',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_CRITERIA',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_GENDER',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_AGE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_EDUCATION',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_ADDITIONAL_EDUCATION',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_WORK_EXPERIENCE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_ADDITIONAL_KNOWLEDGE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_COMPETENCIES',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_LANGUAGE_KNOWLEDGE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_EMPLOYMENT_CONTRACT',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_GPH',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UUF_REMOTE_WORK_GPH',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_TMP_REMOTE_WORK',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_RHR',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_NRD',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_SALARY',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_PERSONAL_PREMIUM',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_INDICATIVE_PREMIUM',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_PREMIUM_PERCENT',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_SOC_PACKAGE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_VACATION',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_SHEDULE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_WORK_ADDRESS',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_ACCEPTING_PERSON',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_MENTOR',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_COMMENT',
                                'optionFlags' => '1',
                            ],
                        ],
                    ],
                    [
                        'name' => 'candidate_block',
                        'title' => 'Финальный кандидат на трудоустройство',
                        'type' => 'section',
                        'elements' => [
                            [
                                'name' => 'UF_CANDIDATE',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_CANDIDATE_DEPARTMENT',
                                'optionFlags' => '1',
                            ],
                            [
                                'name' => 'UF_EMPLOYMENT_DATE',
                                'optionFlags' => '1',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
