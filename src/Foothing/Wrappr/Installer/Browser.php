<?php namespace Foothing\Wrappr\Installer;


class Browser {
    protected $collection = [];

    protected $index;

    protected function next( $item ) {
        $this->index++;
        return $this->current($item);
    }

    protected function reset() {
        $this->collection = [];
        $this->index = -1;
    }

    public function getItems() {
        return $this->collection;
    }

    protected function current($item = null) {
        return $item ? $this->collection[ $this->index ] = $item : $this->collection[ $this->index ];
    }
}