<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Lib;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class LAMVisitor extends NodeVisitorAbstract
{
    private $nodeVisitorHandler;

    public function __construct()
    {
        $this->nodeVisitorHandler = new NodeVisitorHandler;
    }

    public function tree()
    {
        return $this->nodeVisitorHandler->result;
    }

    public function leaveNode(Node $node)
    {
        $nodeHandler = new NodeHandler($node);
        $this->nodeVisitorHandler->handle($nodeHandler);
    }

    public function enterNode(Node $node)
    {
        $nodeHandler = new NodeHandler($node);
        $this->nodeVisitorHandler->handle($nodeHandler, true);
    }
}
