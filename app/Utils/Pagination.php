<?php

    namespace App\Utils;

    class Pagination {
        private $list;
        private $page;
        private $limit;
        private $total;
        private $offset;
        private $count;
        private $paged;
        private $pages;

        public function __construct($list, $page, $limit) {
            $this->setOffset($page, $limit);
            $this->setList($list);

            $this->setPaged();
        }

        private function setOffset($page, $limit) {
            $this->page   = $page;
            $this->limit  = $limit;
            $this->offset = ($page - 1) * $limit;
        }

        private function setList($list) {
            $this->total = count($list);
            $this->list  = $list;
            $this->pages = intval(ceil($this->total / $this->limit));
        }

        private function setPaged() {
            $this->paged = array_splice($this->list, $this->offset, $this->limit);
            $this->count = count($this->paged);
        }

        public function get() {
            return [
                'count' => $this->count,
                'list'  => $this->paged,
                'pages' => $this->pages,
                'page'  => $this->page,
                'limit' => $this->limit,
                'total' => $this->total
            ];
        }

        public function render($request, $components = [], $pages = 2) {
            $data = $this->get();

            $uri = $request->getUri();
            $queryParams = $request->getQueryParams();
            $href = $uri . '?' . http_build_query(array_merge($queryParams, ['page' => '']));

            $defaultComponents = ['number', 'first', 'last', 'previous', 'next', 'ellipsis'];
            
            foreach($defaultComponents as $key) {
                if(!isset($components[$key])) $components[$key] = 'pagination/' . (in_array($key, ['first', 'last']) ? 'number' : $key);
                else $components[$key] = 'pagination/' . $components[$key];
            }

            $pagination = "";

            $first = $data['page'] - $pages;
            $last  = $data['page'] + $pages;

            if($first < 1) {
                $last += $first * -1;
                $first = 1;
            };

            if($last > $data['pages']) $last = $data['pages'];

            if($components['previous'] && $data['page'] != 1)
                $pagination .= Component::render($components['previous'], [
                    'href'    => $href . ($data['page'] - 1),
                    'number'  => $data['page'] - 1
                ]);

            if($components['first'] && $first != 1)
                $pagination .= Component::render($components['first'], [
                    'href'    => $href . 1,
                    'number'  => 1
                ]);

            if($components['ellipsis'] && $first > 1 + 1)
                $pagination .= Component::render($components['ellipsis']);

            for($i = $first; $i <= $last; $i ++) {

                $pagination .= Component::render($components['number'], [
                    'number'  => $i,
                    'href'    => $href . $i,
                    'active'  => $i == $data['page'] ? 'active' : ''
                ]);
            }

            if($components['ellipsis'] && $last < $data['pages'] - 1)
                $pagination .= Component::render($components['ellipsis']);

            if($components['last'] && $last != $data['pages'])
                $pagination .= Component::render($components['last'], [
                    'href'    => $href . $data['pages'],
                    'number'  => $data['pages']
                ]);

            if($components['next'] && $data['page'] != $data['pages'])
                $pagination .= Component::render($components['next'], [
                    'href'    => $href . ($data['page'] + 1),
                    'number'  => $data['page'] + 1
                ]);

            return $pagination;
        }
    }