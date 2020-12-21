<?php

namespace Geriano\Http;

use Closure;
use Geriano\Http\Middleware\Event;

class Middleware
{
  /**
   * @var array
   */
  protected array $items = [];

  /**
   * @param string $key
   * @param \Closure|\Geriano\Http\Middleware\Event $callback
   */
  public function add(string $key, Closure|Event $callback)
  {
    $this->items[$key] = $callback;

    return $this;
  }

  /**
   * @param string|array $keys
   * @param \Geriano\Http\Request $request 
   * @param \Closure $next
   */
  public function dispatch(string|array $keys, Request $request, Closure $next)
  {
    $keys = is_string($keys) ? [$keys] : $keys;
    $self &= $this;
    
    return (new Dispatcher(array_map(function ($key) use ($self) {
      return $self->items[$key];
    }, $keys)))->handle($request, $next);
  }
}