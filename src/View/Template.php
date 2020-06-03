<?php

namespace Psytelepat\Lootbox\View;

use Exception;
use ArrayAccess;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;

class Template implements Renderable, TemplateInterface
{
    protected $data;
    protected $layout;
    protected $properties;

    public function __construct(string $layout = null, Array $data = [])
    {
        if ($layout) {
            $this->layout = $layout;
        }
        $this->data = $data;
    }

    public static function factory(string $layout = null, Array $data = []): Template
    {
        return new static($layout, $data);
    }

    public function set(string $key, $val): Template
    {
        $this->data[$key] = $val;
        return $this;
    }

    public function bind(string $key, &$val): Template
    {
        $this->data[$key] = &$val;
        return $this;
    }

    public function push(string $key, $value): Template
    {
        if (property_exists($this, $key)) {
            if ($this->$key instanceof ArrayAccess) {
                $this->$key[] = $value;
            } else {
                throw new \InvalidArgumentException('Unable to push to non-array property "' . $key . '".');
            }
        } else {
            throw new Exception('Undefined property "' . $key . '".');
        }

        return $this;
    }

    public function render(): string
    {
        $data = $this->data;
        $this->mergeProperties($data);
        return view($this->layout, $data)->render();
    }

    private function mergeProperties(&$data): void
    {
        if (!is_array($this->properties)) {
            return;
        }
        foreach ($this->properties as $property) {
            if (property_exists($this, $property)) {
                $data[$property] = $this->$property;
            }
        }
    }
}
