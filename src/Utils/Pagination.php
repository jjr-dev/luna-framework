<?php

namespace Luna\Utils;

class Pagination
{
    private array $list;
    private int $page;
    private int $limit;
    private int $total;
    private int $offset;
    private int $count;
    private array $paged;
    private int $pages;

    public function __construct(array $list, int $page, int $limit)
    {
        $this->setOffset($page, $limit);
        $this->setList($list);

        $this->setPaged();
    }

    private function setOffset(int $page, int $limit): void
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->offset = ($page - 1) * $limit;
    }

    private function setList(array $list): void
    {
        $this->total = count($list);
        $this->list = $list;
        $this->pages = intval(ceil($this->total / $this->limit));
    }

    private function setPaged(): void
    {
        $this->paged = array_splice($this->list, $this->offset, $this->limit);
        $this->count = count($this->paged);
    }

    public function get(): array
    {
        return [
            'count' => $this->count,
            'list' => $this->paged,
            'pages' => $this->pages,
            'page' => $this->page,
            'limit' => $this->limit,
            'total' => $this->total
        ];
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getList(): array
    {
        return $this->paged;
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function render($request, array $components = [], int $pages = 2): string
    {
        $data = $this->get();

        if ($data['pages'] == 0) {
            return "";
        }

        $uri = $request->getUri();
        $queryParams = $request->query();
        
        if (isset($queryParams['page'])) {
            unset($queryParams['page']);
        }
            
        $href = $uri . '?' . http_build_query(array_merge($queryParams, ['page' => '']));

        $defaultComponents = [
            'number',
            'first',
            'last',
            'previous',
            'next',
            'ellipsis'
        ];
        
        foreach ($defaultComponents as $key) {
            if (!isset($components[$key])) {
                $components[$key] = 'pagination/' . (in_array($key, ['first', 'last']) ? 'number' : $key);
            } else {
                $components[$key] = 'pagination/' . $components[$key];
            }
        }

        $pagination = "";

        $first = $data['page'] - $pages;
        $last = $data['page'] + $pages;

        if ($first < 1) {
            $last += $first * -1;
            $first = 1;
        };

        if ($last > $data['pages']) {
            $last = $data['pages'];
        }

        if ($components['previous'] && $data['page'] != 1) {
            $pagination .= Component::render($components['previous'], [
                'href' => $href . ($data['page'] - 1),
                'number' => $data['page'] - 1
            ]);
        }

        if ($components['first'] && $first != 1) {
            $pagination .= Component::render($components['first'], [
                'href' => $href . 1,
                'number' => 1
            ]);
        }

        if ($components['ellipsis'] && $first > 1 + 1) {
            $pagination .= Component::render($components['ellipsis']);
        }

        for ($i = $first; $i <= $last; $i ++) {
            $pagination .= Component::render($components['number'], [
                'number' => $i,
                'href' => $href . $i,
                'active' => $i == $data['page'] ? 'active' : ''
            ]);
        }

        if ($components['ellipsis'] && $last < $data['pages'] - 1) {
            $pagination .= Component::render($components['ellipsis']);
        }

        if ($components['last'] && $last != $data['pages']) {
            $pagination .= Component::render($components['last'], [
                'href' => $href . $data['pages'],
                'number' => $data['pages']
            ]);
        }

        if ($components['next'] && $data['page'] != $data['pages']) {
            $pagination .= Component::render($components['next'], [
                'href' => $href . ($data['page'] + 1),
                'number' => $data['page'] + 1
            ]);
        }

        return $pagination;
    }
}