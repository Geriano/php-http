<?php

namespace Geriano\Http;

use Closure;
use Geriano\Container\Container;

class Dispathcer
{
  /**
   * @param array $middleware
   */
  public function __construct(
    protected array $middleware,
  ) {}

  /**
   * @param \Geriano\Http\Request $request 
   * @param \Closure $next
   */
  public function handle(Request $request, Closure $next)
  {
    $middleware = array_reverse($this->middleware);

    $complete   = array_reduce($middleware, function (Closure $next, $handler) {
      return $this->create($next, $handler);
    }, $this->next($next));

    return $complete($request);
  }

  /**
   * @param \Closure $next
   * @return \Closure
   */
  public function next(Closure $next)
  {
    return function ($request) use ($next) {
      return $next($request);
    };
  }

  /**
   * @param \Closure $next
   * @param \Closure|\Geriano\Http\Middleware\Event $middleware
   * @return \Closure
   */
  public function create(Closure $next, Closure|Event $middleware)
  {
    return function ($request) use ($next, $middleware) {
      if($middleware instanceof Closure)
        return $middleware($request, $next);

      return Container::getInstance()->make($middleware)->handle($request, $next);
    };
  }
}