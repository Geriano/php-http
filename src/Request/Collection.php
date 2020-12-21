<?php

namespace Geriano\Http\Request;

use ArrayAccess;
use Geriano\Helpers\Arr;
use Geriano\Helpers\Json;
use IteratorAggregate;

class Collection implements ArrayAccess, IteratorAggregate
{
  /**
   * @var array
   */
  protected array $items = [];

  /**
   * @param array $items
   */
  public function __construct(array $items = [])
  {
    $this->items = $items;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->json();
  }

  /**
   * @return array
   */
  public function all() : array 
  {
    return $this->items;
  }

  /**
   * @return \Geriano\Helpers\Json
   */
  public function json()
  {
    return new Json($this->all());
  }

  /**
   * @param string $key 
   * @param mixed $default
   * @return mixed
   */
  public function get(string $key, $default = null)
  {
    return Arr::get($this->items, $key, $default);
  }

  /**
   * @param string $key
   * @param mixed $value
   * @return self
   */
  public function set(string $key, $value)
  {
    Arr::set($this->items, $key, $value);
    
    return $this;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function has(string $key)
  {
    return Arr::has($this->items, $key);
  }

  /**
   * @param string $key
   * @return self
   */
  public function remove(string $key)
  {
    Arr::remove($this->items, $key);

    return $this;
  }

  /**
   * @return \ArrayIterator
   */
  public function getIterator()
  {
    return Arr::iterator($this->items);
  }

  /**
   * @param string|int $offset
   * @return bool
   */
  public function offsetExists($offset)
  {
    return $this->has($offset);
  }

  /**
   * @param string|int $offset
   * @return mixed
   */
  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

  /**
   * @param string|int $offset
   * @param mixed $value
   * @return self
   */
  public function offsetSet($offset, $value)
  {
    return $this->set($offset, $value);
  }

  /**
   * @param string|int $offset
   * @return mixed
   */
  public function offsetUnset($offset)
  {
    return $this->remove($offset);
  }
}