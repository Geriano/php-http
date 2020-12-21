<?php

namespace Geriano\Http;

use Geriano\Container\Injection;
use Geriano\Helpers\Arr;
use Geriano\Helpers\Json;
use Geriano\Http\Request\Collection;
use Geriano\Http\Request\File;
use Geriano\Session\Manager as SessionManager;
use RuntimeException;

class Request
{
  use Injection;

  /**
   * @const string
   */
  const METHOD_PREFIX = '__method';

  /**
   * @var \Geriano\Http\Header
   */
  protected Header $header;

  /**
   * @var \Geriano\Session\Manager
   */
  protected $session;

  /**
   * @param string $method
   * @param \Geriano\Http\URL $url
   * @param \Geriano\Http\Collection $query
   * @param \Geriano\Http\Collection $post
   * @param \Geriano\Http\Collection $files
   * @param \Geriano\Http\Collection $server
   */
  public function __construct(
    protected string $method,
    protected URL $url,
    protected Collection $query,
    protected Collection $post,
    protected Collection $files,
    protected Collection $server,
  )
  {
    $this->header = new Header();

    foreach($server as $key => $val) {
      if(preg_match('/\AHTTP_/', $key)) {
        $this->header->set(
          ucwords(preg_replace('/\_/', '-', preg_replace(
            '/\AHTTP_/', '', $key
          )), '-'),
          $val
        );
      }
    }
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->json();
  }

  /**
   * @param string
   */
  public function __get(string $key)
  {
    if(Arr::has($this->all(), $key)) {
      return Arr::get($this->all(), $key);
    }

    return $this->{$key} ?? null;
  }

  /**
   * @param array $query
   * @param array $post
   * @param array $files
   * @param array $server
   */
  public function duplicate(array $query = [], array $post = [], array $files = [], array $server = [])
  {
    $dup = clone $this;

    $globals = [
      'query'  => $query,
      'post'   => $post,
      'files'  => $files,
      'server' => $server,
    ];
    
    foreach($globals as $global => $parameters) {
      if($parameters) {
        $dup->{$global} = new Collection($parameters);
      }
    }

    return $dup;
  }

  /**
   * 
   */
  public function __clone()
  {
    $this->url     = clone $this->url;
    $this->query   = clone $this->query;
    $this->post    = clone $this->post;
    $this->files   = clone $this->files;
    $this->server  = clone $this->server;
    $this->session = clone $this->session;
    $this->header  = clone $this->header;
  }

  /**
   * @return array
   */
  public function all()
  {
    return Arr::plus(
      $this->query->all(),
      $this->files->all(),
      $this->post->all()
    );
  }

  /**
   * @return \Geriano\Helpers\Json
   */
  public function json()
  {
    return new Json($this->all());
  }

  /**
   * Watch request from global parameters
   * 
   * @return self
   */
  public static function watch() : self
  {
    $globals = [
      'query'  => $_GET,
      'post'   => $_POST,
      'files'  => $_FILES,
      'server' => $_SERVER,
    ];

    foreach($globals as $global => $parameters) {
      if($global === 'files') $parameters = array_map(function ($item) {
        return new File($item);
      }, $parameters);

      $$global = new Collection($parameters);
    }

    $method = mb_strtoupper($post->get(self::METHOD_PREFIX, $server->get('REQUEST_METHOD')));
    $url    = new URL(
      $server->get('REQUEST_SCHEME', 'http'),
      $server->get('SERVER_NAME', 'localhost'),
      (int) $server->get('SERVER_PORT', 80),
      $server->get('REQUEST_URI', '/'),
      $server->get('PATH_INFO', '/')
    );

    $server->set('SERVER_SOFTWARE', 'Geriano Framework');

    return new static($method, $url, $query, $post, $files, $server);
  }

  /**
   * Override global parameters
   */
  public function overrideGlobals()
  {
    $_SERVER = $this->server->all();
    $_FILES  = $this->files->all();
    $_POST   = $this->post->all();
    $_GET    = $this->query->all();

    foreach($this->header->all() as $key => $val) {
      $key = strtoupper(preg_replace(
        '/\-/', '_', $key
      ));

      if(in_array($key, ['CONTENT_LENGTH', 'CONTENT_TYPE', 'CONTENT_MD5'])) {
        $_SERVER[$key] = implode(';', $val);
      } else {
        $_SERVER['HTTP_' . $key] = implode(';', $val);
      }
    }
  }

  /**
   * @param string $global
   * @param string|null $key
   * @param mixed $default
   * @return mixed
   */
  protected function getGlobal(string $global, string $key = null, $default = null)
  {
    if($key) 
      return $this->getGlobal($global)->get($key, $default);

    return $this->{$global};
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function query(string $key = null, $default = null)
  {
    return $this->getGlobal('query', $key, $default);
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function post(string $key = null, $default = null)
  {
    return $this->getGlobal('post', $key, $default);
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function files(string $key = null, $default = null)
  {
    return $this->getGlobal('files', $key, $default);
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function server(string $key = null, $default = null)
  {
    return $this->getGlobal('server', $key, $default);
  }

  /**
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function session(string $key = null, $default = null)
  {
    if($this->hasSession())
      return $this->getGlobal('session', $key, $default);

    throw new RuntimeException('Session is not configured');
  }

  /**
   * @return bool
   */
  public function hasSession()
  {
    return $this->session instanceof SessionManager;
  }

  /**
   * @param \Geriano\Session\Manager
   * @return self
   */
  public function setSession(SessionManager $session)
  {
    $this->session = $session;

    return $this;
  }
}