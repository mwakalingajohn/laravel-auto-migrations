<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Lib;


/**
 * Responsible for traversing the AST taking the important bits.
 */
class NodeVisitorHandler
{

    /**
     * Handles a single node
     */
    private NodeHandler $nodeHandler;

    /**
     * The store for index of each method call inside the Schema callback
     */
    private $methodStatementIndex = [];

    /**
     * The methods available inside the migration class. Any other class will be ignored
     */
    private $mainMethods = ["up", "down"];

    /**
     * Main methods are either 'up' or 'down'. Store which is being traversed currently
     */
    private $currentMainMethod = null;

    /**
     * Main statements are the Schema static calls inside 'up' and  'down' methods
     * The index is responsible of storing which statement node is currently being traversed
     */
    private $mainStatementIndex = [];

    /**
     * The traversed store result
     */
    public $result;

    /**
     * differenciates the origin of the class call. LeaveNode or Enter node inside the visitor
     */
    private $isEnteringNode;

    public function __construct()
    {
        $this->setIndices();
    }

    /**
     * Controls the main logic
     */
    public function handle(NodeHandler $nodeHandler, $isEnteringNode = false)
    {
        $this->nodeHandler = $nodeHandler;
        $this->isEnteringNode = $isEnteringNode;

        $this->getMainMethodName();

        $this->getMainStatement();

        if (!$this->isEnteringNode) {
            $this->getMethodStatements();
        }
    }

    /**
     * Once the main method(Either up or down) is found set it as the current name being traversed
     */
    public function getMainMethodName()
    {
        if ($this->nodeHandler->isMainMethod($this->mainMethods) && $this->isEnteringNode) {
            $this->currentMainMethod = $this->nodeHandler->getName();
        }
    }

    /**
     * Checks whether the current node is a main statement(Schema static call) and sets it
     * to current
     */
    public function getMainStatement()
    {
        if ($this->nodeHandler->isMainStatement() && $this->isEnteringNode) {
            $this->incrementMainStatementIndex();
            $this->appendMainStatementMetaData();
        }
    }

    /**
     * Look for the statements inside the Schema closure, and add them to the result
     * Example: $table->string("name");
     */
    public function getMethodStatements()
    {
        if ($this->nodeHandler->isMethod() && $this->currentMainMethod) {
            if ($this->nodeHandler->isRootMethod()) {
                $this->appendRootStatement();
            } else {
                $this->appendChainedStatement();
            }
        }
    }

    /**
     * Add the static schema expression with its meta data to the result
     */
    public function appendMainStatementMetaData()
    {
        $this->setStatementMetaDataResult([
            "name" => $this->nodeHandler->getStaticClassName(),
            "method" => $this->nodeHandler->getStaticMethodName(),
            "arg" => $this->nodeHandler->getArgumentByIndex(0),
            "statements" => []
        ]);
    }

    /**
     * Append the first function call of an expression inside schema closure
     * Example: $table->string("email")->nullable();
     * Captures the "string("email")" part, with argument "email"
     */
    private function appendRootStatement()
    {
        $this->incrementMethodStatementIndex();
        $this->updateCurrentMainMethod();
        $this->setStatementResult([
            "name" => $this->nodeHandler->getName(),
            "args" => $this->nodeHandler->getArguments(),
            "var"  => $this->nodeHandler->getInstanceVariableName(),
            "chain" => []
        ]);
    }

    /**
     * If a new method statement is found increment the index which will be used
     * to store the method data value
     * Example: when a "$table->string("email")->nullable()" is found
     */
    private function incrementMethodStatementIndex()
    {
        $mainStatementIndex = $this->getCurrentMainStatementIndex();
        $this->methodStatementIndex[$mainStatementIndex]++;
    }

    /**
     * If a new schema expression is found increment the index which will be used
     * to store the schema data
     * Example: when a "Schema::create("user",....)" is found
     */
    private function incrementMainStatementIndex()
    {
        $currentMainMethod = $this->currentMainMethod;
        $this->mainStatementIndex[$currentMainMethod]++;
        $this->initiateMethodStatementIndex();
    }

    /**
     * Change the current main method 'up' or 'down' when it found in the tree
     * i.e when it is the current node
     */
    private function updateCurrentMainMethod()
    {
        $this->mainMethod = $this->nodeHandler->getName();
    }

    /**
     * Add the chained statement found to the result
     * Example: $table->string("email")->nullable();
     * The "nullable()" part is stored with argurments
     */
    private function appendChainedStatement()
    {
        $this->setStatementResult([
            "name" => $this->nodeHandler->getName(),
            "args" => $this->nodeHandler->getArguments(),
        ], true);
    }

    /**
     * Add the schema expression meta data found to the result.
     * The data added is class name(schema), method name(create, dropIfExists),
     * and first argument. The second is always closure.
     */
    private function setStatementMetaDataResult($statementMetaData)
    {
        $currentMainStatementIndex = $this->getCurrentMainStatementIndex();
        $this->result[$this->currentMainMethod][$currentMainStatementIndex] = $statementMetaData;
    }

    /**
     * Add the method data to the tree. Checks if is from chain or it main
     * function call. The indices are abbreviated to keep the statement short
     */
    private function setStatementResult($statementData, $isChain = false)
    {
        $cmsi   = $this->getCurrentMethodStatementIndex();
        $cmasi  = $this->getCurrentMainStatementIndex();
        $cmm    = $this->currentMainMethod;
        $st     = "statements";

        if (!$isChain)
            $this->result[$cmm][$cmasi][$st][$cmsi] = $statementData;
        else
            $this->result[$cmm][$cmasi][$st][$cmsi]["chain"][] = $statementData;
    }

    /**
     * Return the current method statement index, for storing method call data
     * to the tree
     * Method statement example:
     * $table->string("name");
     */
    private function getCurrentMethodStatementIndex()
    {
        $currentMainStatementIndex = $this->getCurrentMainStatementIndex();
        return $this->methodStatementIndex[$currentMainStatementIndex];
    }

    /**
     * The indices for method statements are nested array and require to be
     * initialised manually
     * Method statement example:
     * $table->string("name");
     */
    private function initiateMethodStatementIndex()
    {
        $mainStatementIndex = $this->getCurrentMainStatementIndex();
        $this->methodStatementIndex[$mainStatementIndex] = 0;
    }

    /**
     * Return the index of the current schema expression
     * Schema expression example:
     * Schema::create("users",fun...)
     */
    private function getCurrentMainStatementIndex()
    {
        return $this->mainStatementIndex[$this->currentMainMethod];
    }

    /**
     * Create the initial index of the main statements for storing the
     * schema expressions and children
     * Schema expression example:
     * Schema::create("users",fun...)
     */
    private function setIndices()
    {
        foreach ($this->mainMethods as $method) {
            $this->mainStatementIndex[$method] = 0;
        }
    }
}
