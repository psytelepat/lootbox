<?php

namespace Psytelepat\Lootbox\View;

use Illuminate\Contracts\Support\Renderable;
use Psytelepat\Lootbox\View\RenderableArray;

class Views extends RenderableArray
{
    public function offsetSet($offset, $value)
    {
        if (is_string($value) || $value instanceof Renderable) {
            if (is_null($offset)) {
                $this->items[] = $value;
            } else {
                $this->items[$offset] = $value;
            }
        } else {
            throw new \InvalidArgumentException('$value shoud be string or instance of lluminate\Contracts\Support\Renderable');
        }
    }
}
