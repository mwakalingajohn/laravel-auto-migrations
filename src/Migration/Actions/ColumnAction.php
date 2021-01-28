<?php


namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

abstract class ColumnAction implements Action{

    protected $table;

    protected $didProcess = false;

    public function __construct($table) {
        $this->table = $table;
    }

    public abstract function process($branch, $store);

    public function didProcess() : bool
    {
        return $this->didProcess;
    }

    public function removeColumn($databaseMap, $name)
    {
        unset($databaseMap[$this->table][$name]);
        return $databaseMap;
    }

    public function addColumn($databaseMap, $name, $data)
    {
        $databaseMap[$this->table][$name] = $data;
        return $databaseMap;
    }

    public function changeColumn($databaseMap, $name, $data)
    {
        // to implement
        return $databaseMap;
    }

    public function renameColumn($databaseMap, $from, $to)
    {
        $databaseMap[$this->table] = $this->changeKey($databaseMap[$this->table], $from, $to);
        return $databaseMap;
    }

    /**
     * Change the key of an array. equal to changing table name
     */
    private function changeKey( $array, $old_key, $new_key ) {

        if( ! array_key_exists( $old_key, $array ) )
            return $array;

        $keys = array_keys( $array );
        $keys[ array_search( $old_key, $keys ) ] = $new_key;

        return array_combine( $keys, $array );
    }

    /**
     * Get the name of the column
     */
    public function getColumnName($statement)
    {
        if(array_key_exists(0, $statement["args"])){
            return $statement["args"][0];
        }
        return $statement["name"];
    }

    /**
     * Get data from statement
     */
    public function getData($statement)
    {
        return [
            "type" => $statement["name"],
            "args" => $statement["args"],
            "chain" => $statement["chain"]
        ];
    }
}
