<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration;

use Error;
use Illuminate\Support\Facades\App;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

class Parser{

    /**
     * Parser object
     */
    private $parser;

    /**
     * Files to be parsed
     */
    private $files;

    /**
     * The migration tree store
     */
    private $store;

    /**
     * The AST to array converter
     */
    private AstToArrayConverter $converter;

    public function __construct() {
        $this->parser = $this->getParser();
        $this->converter = $this->getConverter();
    }

    /**
     * Parse all the files provided and return a store tree
     */
    public function parse($files)
    {
        $this->setFiles($files);
        $this->parseFiles();
        return $this->store;
    }

    /**
     * Start parsing the files to get the AST tree
     */
    public function parseFiles()
    {
        $this->parseFile($this->files->first());
        // $this->files->each(function($file){
        //     $this->parseFile($file);
        // });
    }

    /**
     * Parse each single file and add the result to store
     */
    public function parseFile($file)
    {
        $ast = $this->getAST(
            $this->getContents($file)
        );
        $this->converter->convert($ast);
    }

    /**
     * Get the abstract syntax tree
     */
    public function getAST($content)
    {
        try {
            return $this->parser->parse($content);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }
    }

    /**
     * Get parser
     */
    public function getParser()
    {
        return (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * Set files to array
     */
    public function setFiles($files)
    {
        if(is_array($files))
            $this->files = collect($files);
        else
            $this->files = $files;
    }

    /**
     * Get files to parse
     */
    public function getFiles($files)
    {
        return collect($this->files);
    }

    /**
     * Get file contents
     */
    public function getContents($file)
    {
        return file_get_contents($file);
    }

    /**
     * Get the AstToArrayConverter
     */
    private function getConverter(){
        return App::make(AstToArrayConverter::class);
    }
}
