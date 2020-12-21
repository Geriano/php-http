<?php

namespace Geriano\Http\Request;

class File
{
  /**
   * @param array $item
   */
  public function __construct(
    protected array $item,
  ) {}

  /**
   * @return string
   */
  public function name()
  {
    return $this->item['name'];
  }

  /**
   * @return string
   */
  public function type()
  {
    return $this->item['type'];
  }

  /**
   * @return string
   */
  public function extension()
  {
    return pathinfo($this->name(), PATHINFO_EXTENSION);
  }

  /**
   * @return string
   */
  public function tmp()
  {
    return $this->item['tmp_name'];
  }

  /**
   * @return int
   */
  public function error()
  {
    return $this->item['error'];
  }

  /**
   * @return int
   */
  public function size()
  {
    return $this->item['size'];
  }

  /**
   * @param string $path
   */
  public function move(string $path)
  {
    return move_uploaded_file($this->tmp(), $path);
  }
}