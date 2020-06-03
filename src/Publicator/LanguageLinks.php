<?php

namespace Psytelepat\Lootbox\Publicator;

use Psytelepat\Lootbox\Util;

trait LanguageLinks
{
    public function langLinks(): array
    {
        $links = [];
        foreach (Util::$localesById as $localeid => $v) {
            $links[$v['tid']] = [
            'locale' => $v,
            'item' => self::where('grp', $this->grp)->where('lng', $localeid)->first(),
            ];
        }

        foreach ($links as $locale => $link) {
            $link['icon'] = $link['item'] ? 'pencil' : 'plus';
            $link['text'] = $link['locale']['ttl'];
            $link['url']  = $link['item'] ? route('blogger.edit', [ 'grp' => $link['item']->grp ]) : route('blogger.copy', [ 'grp' => $link['item']->grp, 'locale' => $locale ]);
            $links[$locale] = $link;
        }

        return $links;
    }
}
