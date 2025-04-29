<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\DatabaseHandler;

class Session extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Session Driver
     * --------------------------------------------------------------------------
     *
     * The session storage driver to use:
     * - `CodeIgniter\Session\Handlers\FileHandler`
     * - `CodeIgniter\Session\Handlers\DatabaseHandler`
     * - `CodeIgniter\Session\Handlers\MemcachedHandler`
     * - `CodeIgniter\Session\Handlers\RedisHandler`
     */
    public $driver = DatabaseHandler::class;

    /**
     * --------------------------------------------------------------------------
     * Session Cookie Name
     * --------------------------------------------------------------------------
     */
    public $cookieName = 'simedis_session';

    /**
     * --------------------------------------------------------------------------
     * Session Database Group
     * --------------------------------------------------------------------------
     */
    public $DBGroup = 'default';

    /**
     * --------------------------------------------------------------------------
     * Session Database Table
     * --------------------------------------------------------------------------
     */
    public $savePath = 'tbl_sessions';

    /**
     * --------------------------------------------------------------------------
     * Session Expiration
     * --------------------------------------------------------------------------
     */
    public $expiration = 7200;

    /**
     * --------------------------------------------------------------------------
     * Match IP
     * --------------------------------------------------------------------------
     */
    public $matchIP = false;

    /**
     * --------------------------------------------------------------------------
     * Time to Update
     * --------------------------------------------------------------------------
     */
    public $timeToUpdate = 300;

    /**
     * --------------------------------------------------------------------------
     * Regenerate Destroy
     * --------------------------------------------------------------------------
     */
    public $regenerateDestroy = false;
}
