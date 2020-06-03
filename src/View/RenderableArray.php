<?php

namespace Psytelepat\Lootbox\View;

use Illuminate\Contracts\Support\Renderable;

class RenderableArray implements \ArrayAccess, \Iterator, \Countable, Renderable
{
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public static function factory(array $items = []): RenderableArray
    {
        return new static($items);
    }

    protected $items = [];
    protected $position = 0;
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }
    public function rewind()
    {
        $this->position = 0;
    }
    public function current()
    {
        return $this->items[$this->position];
    }
    public function key()
    {
        return $this->position;
    }
    public function next()
    {
        ++$this->position;
    }
    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    public function shift()
    {
        return array_shift($this->items);
    }
    public function count()
    {
        return count($this->items);
    }

    public function __toString(): string
    {
        return $this->render();
    }

    public function render(): string
    {
        $output = '';
        foreach ($this->items as $item) {
            $output .= ( $item instanceof Renderable ) ? $item->render() : (string)$item;
        }
        return $output;
    }
}
