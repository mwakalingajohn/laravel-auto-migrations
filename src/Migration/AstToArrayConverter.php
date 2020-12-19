<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration;

use Illuminate\Support\Arr;
use PhpParser\Node\Expr\{StaticCall, Closure};
use PhpParser\Node\Stmt\{Class_, Expression};

/**
 * Convert Abstract Syntax Tree to a normal array of table column and
 * properties
 * Steps:
 * 1. Find the object with the schema static call
 * 2. Get the static method called
 * 3. Get the table name
 * 4. Get each of the calls inside
 * 5. For each call inside
 *  i.  Get the method called
 *  ii. Get all the arguments passed
 */
class AstToArrayConverter
{

    /**
     * The AST to convert
     */
    private $ast;

    /**
     * Down method name, in migration
     */
    private $downMethod = 'down';

    /**
     * Up method name, in migration
     */
    private $upMethod = 'up';

    /**
     * Schema class name
     */
    private $schemaClassName = "Schema";

    /**
     * Blueprint class name
     */
    private $blueprintClassName = "Blueprint";

    public function convert($ast)
    {
        $this->ast = $ast;
        $schemaObject = $this->findMigrationClass();
        $upMethod = $this->getUpMethod($schemaObject->stmts, $this->upMethod);
        $properties = $this->getMigrationProperties($upMethod);
    }

    /**
     * Get the table properties created inside up method
     */
    private function getMigrationProperties($upMethod)
    {
        $schemaObjects = $this->getSchemaObjects($upMethod);
        $migrationProperties = [];
        foreach ($schemaObjects as $schemaObject) {
            $migrationProperties[] = $this->getSchemaProperties($schemaObject->expr);
        }
        // dump($migrationProperties);
    }

    /**
     * Get the properties defined inside the schema using the table object
     */
    private function getSchemaProperties($schemaObject)
    {
        dump($schemaObject);
        $schemaMethod = $this->getSchemaMethod($schemaObject);
        $tableName = $this->getTableName($schemaObject);
        $properties = $this->getTableProperties($schemaObject);
        return (object)["method" => $schemaMethod, "table" => $tableName, "properties" => $properties];
    }

    /**
     * Get the properties defined from the Blueprint $table property inside Schema
     */
    public function getTableProperties($schemaObject)
    {
        $blueprintFun = $this->getBlueprintFunctions($schemaObject);
        $statements = $this->getBlueprintStatements($blueprintFun);
        $properties = [];
        foreach ($statements as $statement) {
            $properties[] = $this->parseStatement($statement);
        }
        return $properties;
    }

    /**
     * Parse blueprint statemtnts
     */
    private function parseStatement($statement)
    {
        $name = $statement->expr->name->name;
        $args = $this->getStatementArgs($statement->expr);
        return (object)["name" => $name, "args" => $args];
    }

    /**
     * Get arguments from statement
     */
    private function getStatementArgs($statement)
    {
        $args = [];
        if (property_exists($statement, "args")) {
            foreach ($statement->args as  $arg) {
                // $args[] = collect($arg->value)->map(function($value){return dump("asdf",$value);});
                $arg = collect($arg->value);
                $args[] = $arg["value"] ?? null;
            }
        }
        return $args;
    }

    /**
     * Get the blueprint statements
     */
    private function getBlueprintFunctions($schemaObject)
    {
        return Arr::where($schemaObject->args, function ($value) {
            $isClosure = $value->value  instanceof Closure;
            $params = $value->value->params ?? null;
            $hasBlueprint = $params ? collect($params)->first()->type->parts[0] == $this->blueprintClassName : false;
            return $isClosure && $hasBlueprint;
        });
    }

    /**
     * Get blueprint statements
     */
    private function getBlueprintStatements($blueprintFun)
    {
        return collect($blueprintFun)->first()->value->stmts;
    }

    /**
     * Get table name
     */
    public function getTableName($schemaObject)
    {
        return collect($schemaObject->args)->first()->value->value;
    }

    /**
     * Get schema method
     */
    private function getSchemaMethod($schemaObject)
    {
        return $schemaObject->name->name;
    }

    /**
     * Find schema object inside the up method
     */
    private function getSchemaObjects($upMethod)
    {
        return Arr::where($upMethod->stmts, function ($value) {
            $isExpression = $value instanceof Expression;
            $isStaticCall = $value->expr instanceof StaticCall;
            $isSchemaClass = collect($value->expr->class->parts)->first() == $this->schemaClassName;
            return $isExpression && $isStaticCall && $isSchemaClass;
        });
    }

    /**
     * Find the static Schema::$method function call inside migration
     */
    private function findMigrationClass()
    {
        return $this->filterArr($this->ast, Class_::class)->first();
    }

    /**
     * Filter array based on type
     */
    private function filterArr($arr, $type)
    {
        return collect(Arr::where($arr, function ($value) use ($type) {
            return $value instanceof $type;
        }));
    }

    /**
     * Get down method from ast class
     */
    private function getDownMethod($stmts, $downMethod)
    {
        return  collect(Arr::where($stmts, function ($value) use ($downMethod) {
            return $value->name->name == $downMethod;
        }))->first();
    }

    /**
     * Get migration up method from ast class
     */
    private function getUpMethod($stmts, $upMethod)
    {
        return  collect(Arr::where($stmts, function ($value) use ($upMethod) {
            return $value->name->name == $upMethod;
        }))->first();
    }
}
