<?php

namespace Illuminate\Pagination {

    use Traversable;

    class Paginator implements \ArrayAccess, \Countable, \IteratorAggregate {

        protected $items = [];

        public function __construct( array $items )
        {
            $this->items = $items;
        }

        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Retrieve an external iterator
         * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
         * @return Traversable An instance of an object implementing <b>Iterator</b> or
         * <b>Traversable</b>
         */
        public function getIterator()
        {
            return new \ArrayIterator($this->items);
        }

        /**
         * (PHP 5 &gt;= 5.1.0)<br/>
         * Count elements of an object
         * @link http://php.net/manual/en/countable.count.php
         * @return int The custom count as an integer.
         * </p>
         * <p>
         * The return value is cast to an integer.
         */
        public function count()
        {
            return count($this->items);
        }

        /**
         * Determine if the given item exists.
         *
         * @param  mixed  $key
         * @return bool
         */
        public function offsetExists($key)
        {
            return array_key_exists($key, $this->items);
        }

        /**
         * Get the item at the given offset.
         *
         * @param  mixed  $key
         * @return mixed
         */
        public function offsetGet($key)
        {
            return $this->items[$key];
        }

        /**
         * Set the item at the given offset.
         *
         * @param  mixed  $key
         * @param  mixed  $value
         * @return void
         */
        public function offsetSet($key, $value)
        {
            $this->items[$key] = $value;
        }

        /**
         * Unset the item at the given key.
         *
         * @param  mixed  $key
         * @return void
         */
        public function offsetUnset($key)
        {
            unset($this->items[$key]);
        }

        public function links()
        {
            return '';
        }

        public function getCurrentPage()
        {
            return 0;
        }

        public function getTotal()
        {
            return $this->count();
        }
    }
}

namespace Illuminate\Support {
    use Traversable;

    class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {

        protected $items = [];

        public function __construct( array $items )
        {
            $this->items = $items;
        }

        /**
         * (PHP 5 &gt;= 5.0.0)<br/>
         * Retrieve an external iterator
         * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
         * @return Traversable An instance of an object implementing <b>Iterator</b> or
         * <b>Traversable</b>
         */
        public function getIterator()
        {
            return new \ArrayIterator($this->items);
        }

        /**
         * (PHP 5 &gt;= 5.1.0)<br/>
         * Count elements of an object
         * @link http://php.net/manual/en/countable.count.php
         * @return int The custom count as an integer.
         * </p>
         * <p>
         * The return value is cast to an integer.
         */
        public function count()
        {
            return count($this->items);
        }

        /**
         * Determine if the given item exists.
         *
         * @param  mixed  $key
         * @return bool
         */
        public function offsetExists($key)
        {
            return array_key_exists($key, $this->items);
        }

        /**
         * Get the item at the given offset.
         *
         * @param  mixed  $key
         * @return mixed
         */
        public function offsetGet($key)
        {
            return $this->items[$key];
        }

        /**
         * Set the item at the given offset.
         *
         * @param  mixed  $key
         * @param  mixed  $value
         * @return void
         */
        public function offsetSet($key, $value)
        {
            $this->items[$key] = $value;
        }

        /**
         * Unset the item at the given key.
         *
         * @param  mixed  $key
         * @return void
         */
        public function offsetUnset($key)
        {
            unset($this->items[$key]);
        }

    }
}
