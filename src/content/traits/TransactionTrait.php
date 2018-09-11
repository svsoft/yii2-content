<?php


namespace svsoft\yii\content\traits;

use yii\db\Transaction;

/**
 * Class MigrationTrait
 * @package svsoft\yii\content\traits
 */
trait TransactionTrait
{
    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     *
     */
    protected function beginTransaction()
    {
        $this->transaction = \Yii::$app->db->beginTransaction();
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function rollBackTransaction()
    {
        if ($this->transaction)
            $this->transaction->rollBack();

        return false;
    }

    /**
     * @throws \yii\db\Exception
     */
    protected function commitTransaction()
    {
        if ($this->transaction)
            $this->transaction->commit();
    }
}
