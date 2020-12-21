<?php

namespace Geriano\Http\Middleware;

use Geriano\Encryption\Encryptor;
use Geriano\Helpers\Str;
use Geriano\Http\Request;
use Geriano\Session\Manager;
use RuntimeException as MissingCSRFToken;
use UnexpectedValueException as InvalidCSRFToken;

class CSRFToken extends Event
{
  /**
   * @param \Geriano\Encryption\Encryptor
   */
  public function __construct(
    protected Encryptor $encryptor,
    protected Manager $session,
  ) {}

  /**
   * Generate random token
   * 
   * @return string
   */
  public function generate()
  {
    if($this->session->has('X-CSRF-TOKEN'))
      $this->session->remove('X-CSRF-TOKEN');

    $this->session->flash('X-CSRF-TOKEN', $this->encryptor->encrypt(
      $random = Str::random(16)
    ));

    return $random;
  }

  /**
   * Check token is valid
   * 
   * @param string $token
   * @return bool
   */
  public function check(string $token)
  {
    $hash = $this->session->get('X-CSRF-TOKEN');

    return $this->encryptor->decrypt($hash) === $token;
  }
}