<?php

namespace Geriano\Http;

class URL
{
  /**
   * @param string $scheme
   * @param string $host
   * @param int $port
   * @param string $uri
   * @param string $path
   */
  public function __construct(
    protected string $scheme,
    protected string $host,
    protected int $port,
    protected string $uri,
    protected string $path,
  ) {}

  /**
   * @return string
   */
  public function __toString()
  {
    return sprintf('%s%s', $this->base(), $this->uri());
  }

  /**
   * @return string
   */
  public function base()
  {
    return sprintf('%s://%s', $this->scheme(), $this->domain());
  }

  /**
   * @return string
   */
  public function domain()
  {
    return $this->host . match($this->port()) {
      80 => '',
      default => sprintf(':%d', $this->port())
    };
  }

  /**
   * @return string
   */
  public function scheme()
  {
    return $this->scheme;
  }

  /**
   * @return string
   */
  public function host()
  {
    return $this->host;
  }

  /**
   * @return int
   */
  public function port()
  {
    return $this->port;
  }

  /**
   * @return string
   */
  public function uri()
  {
    return sprintf('/%s', trim($this->uri, '/'));
  }

  /**
   * @return string
   */
  public function path()
  {
    return sprintf('/%s', trim($this->path, '/'));
  }
}