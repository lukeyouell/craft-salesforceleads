<?php

namespace lukeyouell\salesforceleads\services;

use lukeyouell\salesforceleads\SalesforceLeads;
use lukeyouell\salesforceleads\models\Log as LogModel;
use lukeyouell\salesforceleads\records\Log as LogRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;

use yii\base\Exception;
use yii\base\InvalidConfigException;

class LogService extends Component
{
    // Public Methods
    // =========================================================================

    public function getLogs()
    {
        $rows = $this->_createLogQuery()
            ->all();

        $logs = [];

        foreach ($rows as $row) {
            $logs[] = new LogModel($row);
        }

        return $logs;
    }

    public function insertLog($status = 'success', $details = '')
    {
        $log = new LogModel();

        $log->status  = $status;
        $log->details = $details;

        // Save it
        $save = $this->saveLog($log);

        // Delete old logs
        $this->deleteOldLogs();

        return true;
    }

    public function saveLog(LogModel $model, bool $runValidation = true)
    {
        $record = new LogRecord();

        if ($runValidation && !$model->validate()) {
            Craft::info('Log not saved due to a validation error.', __METHOD__);
            return false;
        }

        $record->status   = $model->status;
        $record->details  = $model->details;

        // Save it
        $record->save(false);

        // Now that we have a record id, save it on the model
        $model->id = $record->id;

        return true;
    }

    public function deleteOldLogs()
    {
        $models = LogRecord::find()
            ->offset(50)
            ->orderBy('dateCreated desc')
            ->all();

        foreach ($models as $model) {
            $model->delete();
        }
    }

    // Private Methods
    // =========================================================================

    private function _createLogQuery()
    {
        return (new Query())
            ->select([
                'sf_logs.id',
                'sf_logs.dateCreated',
                'sf_logs.status',
                'sf_logs.details',
            ])
            ->orderBy('dateCreated desc')
            ->from(['{{%sf_logs}}']);
    }
}
