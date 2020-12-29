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

    public $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function isMethod()
    {
        return $this->node instanceof MethodCall;
    }

    public function getName()
    {
        return $this->node->name->toString();
    }

    public function getInstanceVariableName()
    {
        return $this->node->var->name;
    }

    public function isMainMethod($mainMethods)
    {
        return $this->node instanceof ClassMethod &&
            in_array($this->node->name->toString(), $mainMethods);
    }

    public function isRootMethod()
    {
        return $this->node->var instanceof Variable;
    }

    public function isExprression()
    {
        return $this->node instanceof Expression;
    }

    public function isStaticExpression()
    {
        if (!$this->isExprression())
            return false;
        return $this->node->expr instanceof StaticCall;
    }

    public function isMainstatement()
    {
        if (!$this->isStaticExpression())
            return false;
        return $this->getStaticClassName();
    }

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

    public function getStaticMethodName()
    {
        return $this->node->expr->name->toString();
    }

    public function getArguments()
    {
        $arguments = [];
        foreach ($this->node->args as $arg) {
            $arguments[] = $this->getArgument($arg);
        }
        return $arguments;
    }

    public function getArgumentByIndex($index)
    {
        return $this->getArgument($this->node->expr->args[$index]);
    }

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
