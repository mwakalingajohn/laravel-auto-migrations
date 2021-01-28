<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

class ColumnAdd extends ColumnAction
{

    public function process($statement, $store)
    {
        $this->didProcess = true;
        return $this->addColumn(
            $store,
            $this->getColumnName($statement),
            $this->getData($statement)
        );
    }
}
