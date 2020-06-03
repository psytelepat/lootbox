<?php

namespace Psytelepat\Lootbox;

class DotArray implements \ArrayAccess, \Iterator, \Countable
{
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public static function factory($items = []): DotArray
    {
        return new static($items);
    }

    protected $items = [];
    protected $position = 0;

    // ArrayAccess

    public function offsetSet($offset, $value): void
    {
        $path = explode('.', $offset);
        $count = count($path);
        $target = &$this->items;

        if (is_null($offset)) {
            throw new Exception('Invalid DotArray offset');
        }

        while (strlen($trg = array_shift($path))) {
            $count--;
            if ($count) {
                if (!array_key_exists($trg, $target)) {
                    $target[$trg] = [];
                }
                $target = &$target[$trg];
            } else {
                $target[$trg] = $value;
            }
        }
    }

    public function offsetExists($offset): bool
    {
        $path = explode('.', $offset);
        $count = count($path);
        $target = &$this->items;
        while (strlen($trg = array_shift($path))) {
            if (!array_key_exists($trg, $target)) {
                return false;
            }
            $target = &$target[$trg];
        }
        return true;
    }

    public function offsetUnset($offset): void
    {
        $path = explode('.', $offset);
        $count = count($path);
        $target = &$this->items;

        while (strlen($trg = array_shift($path))) {
            $count--;
            if ($count) {
                if (!array_key_exists($trg, $target)) {
                    return;
                }
                $target = &$target[$trg];
            } else {
                unset($target[$trg]);
            }
        }
    }

    public function offsetGet($offset)
    {
        $path = explode('.', $offset);
        $count = count($path);
        $target = &$this->items;

        while (strlen($trg = array_shift($path))) {
            $count--;
            if ($count) {
                if (!array_key_exists($trg, $target)) {
                    return null;
                }
                $target = &$target[$trg];
            } else {
                return $target[$trg];
            }
        }
    }

    // Iterator

    public function current()
    {
        return current($this->items);
    }

    public function key(): scalar
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind(): bool
    {
        return reset($this->items);
    }

    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    // Countable

    public function count(): scalar
    {
        return count($this->items);
    }

    public function shift()
    {
        return array_shift($this->items);
    }

    public function __toString(): string
    {
        return json_encode($this->items, JSON_UNESCAPED_UNICODE);
    }

    public function export()
    {
        return var_export($this->items, true);
    }
}
