<?php

namespace Geriano\Http\Response;

use Geriano\Helpers\Json;
use UnexpectedValueException;

class Body
{
  /**
   * @var string $body
   */
  protected string $body;

  /**
   * @param mixed $body
   */
  public function __construct($body)
  {
    $this->body = $this->validate($body);
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->body;
  }

  /**
   * @param mixed $body
   * @return self
   */
  public function append($body)
  {
    $this->body .= $this->validate($body);

    return $this;
  }

  /**
   * @param mixed $body
   * @return self
   */
  public function prepend($body)
  {
    $this->body = $this->validate($body) . $this->body;

    return $this;
  }

  /**
   * @param mixed $body
   * @return string
   */
  public function validate($body) : string
  {
    if(
      ! is_null($body) and 
      ! is_array($body) and
      ! is_string($body) and
      ! is_numeric($body) and 
      ! is_callable([$body, '__toString'])
    ) {
      throw new UnexpectedValueException(sprintf(
        'Response body must be null, array, string, numeric or callable with method __toString'
      ));
    }

    return is_array($body) ? new Json($body) : (string) $body;
  }
}