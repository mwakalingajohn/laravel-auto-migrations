<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Lib;


class NodeVisitorHandler
{

    private $nodeHandler;

    private $methodStatementIndex = [];

    private $mainMethods = ["up", "down"];

    private $currentMainMethod = null;

    private $mainStatementIndex = [];

    public $result;

    private $isEnteringNode;

    public function __construct()
    {
        $this->setIndices();
    }

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

    public function getMainMethodName()
    {
        if ($this->nodeHandler->isMainMethod($this->mainMethods) && $this->isEnteringNode) {
            $this->currentMainMethod = $this->nodeHandler->getName();
        }
    }

    public function getMainStatement()
    {
        if ($this->nodeHandler->isMainStatement() && $this->isEnteringNode) {
            $this->incrementMainStatementIndex();
            $this->appendMainStatementMetaData();
        }
    }

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

    public function appendMainStatementMetaData()
    {
        $this->setStatementMetaDataResult([
            "name" => $this->nodeHandler->getStaticClassName(),
            "method" => $this->nodeHandler->getStaticMethodName(),
            "arg" => $this->nodeHandler->getArgumentByIndex(0),
            "statements" => []
        ]);
    }

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

    private function incrementMethodStatementIndex()
    {
        $mainStatementIndex = $this->getCurrentMainStatementIndex();
        $this->methodStatementIndex[$mainStatementIndex]++;
    }

    private function incrementMainStatementIndex()
    {
        $currentMainMethod = $this->currentMainMethod;
        $this->mainStatementIndex[$currentMainMethod]++;
        $this->initiateMethodStatementIndex();
    }

    private function updateCurrentMainMethod()
    {
        $this->mainMethod = $this->nodeHandler->getName();
    }

    private function appendChainedStatement()
    {
        $this->setStatementResult([
            "name" => $this->nodeHandler->getName(),
            "args" => $this->nodeHandler->getArguments(),
        ]);
    }

    private function setStatementMetaDataResult($statementMetaData)
    {
        $currentMainStatementIndex = $this->getCurrentMainStatementIndex();
        $this->result[$this->currentMainMethod][$currentMainStatementIndex] = $statementMetaData;
    }

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

    private function getCurrentMethodStatementIndex()
    {
        $currentMainStatementIndex = $this->getCurrentMainStatementIndex();
        return $this->methodStatementIndex[$currentMainStatementIndex];
    }

    private function initiateMethodStatementIndex()
    {
        $mainStatementIndex = $this->getCurrentMainStatementIndex();
        $this->methodStatementIndex[$mainStatementIndex] = 0;
    }

    private function getCurrentMainStatementIndex()
    {
        return $this->mainStatementIndex[$this->currentMainMethod];
    }

    private function setIndices()
    {
        foreach ($this->mainMethods as $method) {
            $this->mainStatementIndex[$method] = 0;
        }
    }
}
