<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Lib;

use PhpParser\{Node};
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;

class NodeHandler
{

    /**
     * The current node being traversed
     */
    public Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * Checks whether the current node is a method
     * Example: $table->id()
     */
    public function isMethod()
    {
        return $this->node instanceof MethodCall;
    }

    /**
     * Return the name of the method
     * Example: $table->id()
     * Return "id"
     */
    public function getName()
    {
        return $this->node->name->toString();
    }

    /**
     * Return the name of class instance
     * Example: $table->id()
     * Return "table". Instance of Blueprint class
     */
    public function getInstanceVariableName()
    {
        return $this->node->var->name;
    }

    /**
     * Checks if this node is a main method 'up' or 'down'
     */
    public function isMainMethod($mainMethods)
    {
        return $this->node instanceof ClassMethod &&
            in_array($this->node->name->toString(), $mainMethods);
    }

    /**
     * Check if the current node is a root method call
     * Example: $table->string("name")->nullable()
     * "string" is root method, "nullable" is a chained method
     */
    public function isRootMethod()
    {
        return $this->node->var instanceof Variable;
    }

    /**
     * Checks if the current node is an expression
     * Example: Schema::create(....);
     * Returns true
     */
    public function isExprression()
    {
        return $this->node instanceof Expression;
    }

    /**
     * Checks if the current node is a static expression
     * Example: Schema::create(....);
     * Returns true
     */
    public function isStaticExpression()
    {
        if (!$this->isExprression())
            return false;
        return $this->node->expr instanceof StaticCall;
    }

    /**
     * Checks if the express is a main statement, a schema expression
     * Example: Schema::create(....);
     * Returns true
     */
    public function isMainstatement()
    {
        if (!$this->isStaticExpression())
            return false;
        return $this->getStaticClassName() == "Schema";
    }

    /**
     * Returns the name of the static class, Schema in the case of
     * migrations
     */
    public function getStaticClassName()
    {
        $classNode = $this->node->expr->class;
        if ($classNode instanceof Name) {
            return $classNode->getLast();
        } else if ($classNode instanceof FullyQualified) {
            return $classNode->getLast();
        }
        return "ClassNotFound";
    }

    /**
     * Returns the name of method called statically
     * Example: Schema::create(....);
     * Returns "create"
     */
    public function getStaticMethodName()
    {
        return $this->node->expr->name->toString();
    }

    /**
     * Get the arguments of a function call
     * Returns an array of arguments
     */
    public function getArguments()
    {
        $arguments = [];
        foreach ($this->node->args as $arg) {
            $arguments[] = $this->getArgument($arg);
        }
        return $arguments;
    }

    /**
     * Get an argument by index
     * Example: string("name",100)
     * Passing index 1 returns 100
     */
    public function getArgumentByIndex($index)
    {
        return $this->getArgument($this->node->expr->args[$index]);
    }

    /**
     * Get an argument value from the AST Arg class
     */
    private function getArgument(Arg $arg)
    {
        $argValue = $arg->value;

        if ($argValue instanceof LNumber || $argValue instanceof String_) {
            return $argValue->value;
        }

        if ($argValue instanceof ConstFetch) {
            return $argValue->name->toString() == "true";
        }
    }
}
