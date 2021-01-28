<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

class ColumnRemove extends ColumnAction
{

    /**
     * Method name for this action
     */
    private $action = "dropColumn";

    public function process($statement, $store)
    {
        if ($statement["name"] == $this->action) {
            $this->didProcess = true;
            return $this->removeColumn($store, $this->getColumnName($statement));
        }
        return $store;
    }
}
