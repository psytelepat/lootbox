<?php

namespace Psytelepat\Lootbox\View;

interface TemplateInterface
{
    public static function factory(string $layout = null, Array $data = []): Template;
    public function set(string $key, $val): Template;
    public function bind(string $key, &$val): Template;
    public function push(string $key, $value): Template;
    public function render(): string;
}
