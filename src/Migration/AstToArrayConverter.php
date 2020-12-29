<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration;

use Illuminate\Support\Arr;
use MwakalingaJohn\LaravelAutoMigrations\Lib\LAMVisitor;
use PhpParser\Node\Expr\{StaticCall, Closure};
use PhpParser\Node\Stmt\{Class_, Expression};
use PhpParser\NodeTraverser;

/**
 * Convert Abstract Syntax Tree to a normal array of table column and
 * properties
 */
class AstToArrayConverter
{
    /**
     * Handle the conversion process to change the AST to a recognizable
     * array
     */
    public function convert($ast)
    {
        /**
         * Node traverser that will be used to traverse
         * the tree
         */
        $traverser = new NodeTraverser;

        /**
         * Custom node visitor implementation for traversing the entire AST
         * - All the magic happens here :D
         */
        $visitor = new LAMVisitor;

        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        return $visitor->tree();
    }
}
