<?php

namespace Psytelepat\Lootbox\View;

use Psytelepat\Lootbox\View\RenderableArray;
use Illuminate\Support\Arr;

class Scripts extends RenderableArray
{
    public function offsetSet($offset, $value): void
    {
        if (is_string($value) || is_array($value)) {
            if (is_null($offset)) {
                $this->items[] = $value;
            } else {
                $this->items[$offset] = $value;
            }
        } else {
            throw new \InvalidArgumentException('$value must be a string or an array.');
        }
    }

    public function render(): string
    {
        $output = '';
        $v = 'v=' . env('APP_VER', time());
        foreach ($this->items as $item) {
            if (is_string($item)) {
                $path = $item;
                $async = false;
            } elseif (is_array($item)) {
                $path = Arr::get($item, 'path');
                $async = Arr::get($item, 'async');
            } else {
                continue;
            }

            $output .= '<script' . ( $async ? ' async' : '' ) . ' type="text/javascript" src="' . asset($path . ( strpos($path, '?') === false ? '?' : '&' ) . $v) . '"></script>' . "\n";
        }
        return $output;
    }
}
