<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

class ColumnRename extends ColumnAction
{

    /**
     * Name of method for this action
     */
    private $action = "renameColumn";

    public function process($statement, $store)
    {
        if ($statement["name"] == $this->action) {
            $this->didProcess = true;
            $renameData = $this->getRenameData($statement);
            return $this->renameColumn(
                $store,
                $renameData->from,
                $renameData->to
            );
        }
        return $store;
    }

    /**
     * Get the column changing from which name to which
     */
    private function getRenameData($statement)
    {
        try {
            return (object)[
                "from" => $statement["args"][0],
                "to" => $statement["args"][1],
            ];
        } catch (\Throwable $th) {
            dd("ERR ColumnRename Ln 36: Data incorrectly formatted");
        }
    }
}
