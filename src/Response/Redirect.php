<?php

namespace Geriano\Http\Response;

use Geriano\Http\Response;

class Redirect extends Response
{
  /**
   * @param string $location
   * @param int $code
   * @param array $headers
   */
  public function __construct(string $location = '/', int $code = 302, array $headers = [])
  {
    parent::__construct(code: $code, headers: array_replace($headers, [
      'Location' => $location
    ]));
  }

  /**
   * 
   */
  public function back()
  {
    $this->header->set('Location', $_SERVER['HTTP_REFERER'] ?? '/', true);

    return $this;
  }
}