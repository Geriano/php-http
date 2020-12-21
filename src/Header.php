<?php

namespace Geriano\Http;

use DateTime;
use Geriano\Helpers\Arr;

class Header
{
  /**
   * @var array
   */
  protected array $items = [];

  /**
   * @param array $headers
   */
  public function __construct(array $headers = [])
  {
    foreach($headers as $header => $value) {
      $this->set($header, $value);
    }

    if(! $this->has('Content-Type'))
      $this->set('Content-Type', ['application/html', 'charset=utf-8']);
      
    if(! $this->has('Content-Language'))
      $this->set('Content-Language', 'en');

    if(! $this->has('Cache-Control'))
      $this->set('Cache-Control', ['no-cache', 'max-age=0']);

    if(! $this->has('Date'))
      $this->set('Date', sprintf(
        '%s GMT', (new DateTime())->format('D, d M Y H:i:s')
      ));
  }

  /**
   * @return string
   */
  public function __toString()
  {
    $max = max(array_map(function ($header) {
      return mb_strlen($header);
    }, array_keys($this->items)));

    $str = '';

    foreach($this->items as $header => $value)
      $str .= sprintf("%-{$max}s:%s\n", $header, implode(';', $value));

    return $str;
  }

  /**
   * @return array
   */
  public function all() : array
  {
    return $this->items;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function has(string $key)
  {
    $key = ucwords(mb_strtolower($key), '-');

    return Arr::has($this->items, $key, false);
  }

  /**
   * @param string $key
   * @param array|string|int $value
   * @param bool $replace
   */
  public function set(string $key, $value, bool $replace = true)
  {
    $key = ucwords(mb_strtolower($key), '-');

    $value = Arr::wrap($value);

    if($this->has($key)) {
      if($replace) {
        $this->items[$key] = $value;
      } else {
        $this->items[$key] = array_merge($this->items[$key], $value);
      }
    } else {
      $this->items[$key] = $value;
    }

    return $this;
  }

  /**
   * @param string $key
   * @param bool $first
   * @return mixed
   */
  public function get(string $key, $default = null, bool $first = true)
  {
    $key = ucwords(mb_strtolower($key), '-');

    if($this->has($key)) {
      if($first) return $this->items[$key][0];

      return $this->items[$key];
    }

    return $default;
  }

  /**
   * @param string $key
   * @return self
   */
  public function remove(string $key) : self
  {
    $key = ucwords(mb_strtolower($key), '-');

    Arr::remove($this->items, $key, false);

    return $this;
  }
}