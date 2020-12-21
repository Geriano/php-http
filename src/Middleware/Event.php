<?php

namespace Geriano\Http\Middleware;

use Closure;
use Geriano\Http\Request;

abstract class Event
{
  /**
   * @param \Geriano\Http\Request $request
   * @param \Closure $next
   * @return \Geriano\Http\Request
   */
  abstract public function handle(Request $request, Closure $next);
}