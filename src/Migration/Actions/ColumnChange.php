<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

class ColumnChange extends ColumnAction
{

    public function process($statement, $store)
    {
        if ($this->isColumnChanged()) {
            $this->didProcess = true;
            return $this->changeColumn(
                $store,
                $this->getColumnName($statement),
                $this->getData($statement)
            );
        }
        return $store;
    }

    private function isColumnChanged()
    {
        return false;
    }
}
