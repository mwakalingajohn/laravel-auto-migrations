<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Lib;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * A class that is used during traversal of the AST
 */
class LAMVisitor extends NodeVisitorAbstract
{
    /**
     * Handles the complex logic that goes behind traversal to get the tree.
     * @param NodeVisitorHandler nodeVisitorHandler
     */
    private NodeVisitorHandler $nodeVisitorHandler;

    public function __construct()
    {
        $this->nodeVisitorHandler = new NodeVisitorHandler;
    }

    /**
     * Return the tree after the tree is parsed
     */
    public function tree()
    {
        return $this->nodeVisitorHandler->result;
    }

    /**
     * It is called after a node has been traversed
     */
    public function leaveNode(Node $node)
    {
        $nodeHandler = new NodeHandler($node);
        $this->nodeVisitorHandler->handle($nodeHandler);
    }

    /**
     * It is called after a node is found
     */
    public function enterNode(Node $node)
    {
        $nodeHandler = new NodeHandler($node);
        $this->nodeVisitorHandler->handle($nodeHandler, true);
    }
}
