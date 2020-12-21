<?php

namespace Geriano\Http;

use Geriano\Helpers\Json;
use Geriano\Http\Response\Body;
use Geriano\Http\Response\Redirect;

class Response
{
  /**
   * @var \Geriano\Http\Response\Body
   */
  protected Body $body;

  /**
   * @var \Geriano\Http\Header
   */
  protected Header $header;

  /**
   * @var int
   */
  protected $code = 200;

  /**
   * @var string
   */
  protected string $reason;

  /**
   * @var array
   */
  protected array $messages = [
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Unused',
    307 => 'Temporary Redirect',
    308 => 'Permanent Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m A Teapot',
    419 => 'Authentication Timeout',
    420 => 'Enhance Your Calm',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    424 => 'Method Failure',
    425 => 'Unordered Collection',
    426 => 'Upgrade Required',
    428 => 'Precondition Required',
    429 => 'Too Many Requests',
    431 => 'Request Header Fields Too Large',
    444 => 'No Response',
    449 => 'Retry With',
    450 => 'Blocked by Windows Parental Controls',
    451 => 'Unavailable For Legal Reasons',
    494 => 'Request Header Too Large',
    495 => 'Cert Error',
    496 => 'No Cert',
    497 => 'HTTP to HTTPS',
    499 => 'Client Closed Request',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    508 => 'Loop Detected',
    509 => 'Bandwidth Limit Exceeded',
    510 => 'Not Extended',
    511 => 'Network Authentication Required',
    598 => 'Network Read Timeout Error',
    599 => 'Network Connect Timeout Error',
  ];

  /**
   * @param mixed $body
   * @param int $code
   * @param array $headers
   */
  public function __construct($body = null, int $code = 200, array $headers = [])
  {
    $this->body   = new Body($body);
    $this->header = new Header($headers);

    if(is_array($body) || $body instanceof Request || $body instanceof Json)
      $this->header->set('Content-Type', 'application/json');

    $this->status($code);
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->body;
  }
  
  /**
   * @param string
   * @return mixed
   */
  public function __get(string $key)
  {
    return $this->{$key} ?? null;
  }

  /**
   * @param mixed $body
   * @return \Geriano\Http\Response\Body
   */
  public function body($body = null)
  {
    return is_null($body) ? $this->body : $this->body = new Body($body);
  }

  /**
   * @return \Geriano\Http\Response\Header
   */
  public function header()
  {
    return $this->header;
  }

  /**
   * @param int $code
   * @param string $reason
   */
  public function status(int $code, string $reason = null)
  {
    if($code > 599 || $code < 100)
      $code = 500;

    $this->code   = $code;
    $this->reason = $this->messages[$this->code] ?? 'Unknown';

    return $this;
  }

  /**
   * 
   */
  public function send()
  {
    if(! headers_sent()) {
      $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
      $reason   = $this->reason;
      $code     = $this->code;

      foreach($this->header->all() as $header => $value) {
        foreach($value as $val) {
          header($header . ':' . $val, true, $code);
        }
      }

      header(sprintf('%s %d %s', $protocol, $code, $reason), true, $code);
    }

    echo $this;
  }

  /**
   * @param string $location
   * @param int $code
   * @param array $headers
   */
  public static function redirect(string $location = '/', int $code = 302, array $headers = [])
  {
    return new Redirect($location, $code, $headers);
  }
}