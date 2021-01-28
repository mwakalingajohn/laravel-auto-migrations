<?php

namespace MwakalingaJohn\LaravelAutoMigrations\Migration\Actions;

interface Action{
    public function process($branch, $store);
    public function didProcess() : bool;
}
