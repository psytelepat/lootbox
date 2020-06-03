<?php

namespace Psytelepat\Lootbox\View;

use Psytelepat\Lootbox\View\RenderableArray;

class Styles extends RenderableArray
{
    public function offsetSet($offset, $value): void
    {
        if (is_string($value)) {
            if (is_null($offset)) {
                $this->items[] = $value;
            } else {
                $this->items[$offset] = $value;
            }
        } else {
            throw new \InvalidArgumentException('$value must be a string.');
        }
    }

    public function render(): string
    {
        $output = '';
        $v = 'v=' . env('APP_VER', time());
        foreach ($this->items as $item) {
            $output .= '<link rel="stylesheet" type="text/css" href="'. asset($item . ( strpos($item, '?') === false ? '?' : '&' ) . $v) .'" />' . "\n";
        }
        return $output;
    }
}
