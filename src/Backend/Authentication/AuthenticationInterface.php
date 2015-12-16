<?php

/**
 * @file
 * Contains \Drupal\integration\Backend\Authentication\AuthenticationInterface.
 */

namespace Drupal\integration\Backend\Authentication;

/**
 * Interface AuthenticationInterface.
 *
 * @package Drupal\integration\Backend\Authentication
 */
interface AuthenticationInterface {

  /**
   * Authenticates and provides an authentication result
   *
   * @return bool
   *    TRUE if authenticated, FALSE otherwise
   */
  public function authenticate();

}
