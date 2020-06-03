<?php
namespace psytelepat\Blogger;

use Illuminate\Support\Facades\App;
use Illuminate\Pagination\LengthAwarePaginator;

class PrettyPaginator extends LengthAwarePaginator
{
    public function url(int $page): string
    {
        if ($page <= 0) {
            $page = 1;
        }

        $parameters = [];
        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return $this->path
                .'/page/'
                .$page
                .urldecode(http_build_query($parameters, null, '&'))
                .$this->buildFragment();
    }
}
