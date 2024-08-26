<?php

/**
 * NativeSession.php - Session file
 *
 * This file is part common support.
 *-----------------------------------------------------------------------------*/

namespace App\Yantrana\__Laraware\Services\NativeSession;

use Exception;

class NativeSession
{
    /**
     * Constructor.
     *-----------------------------------------------------------------------*/
    public function __construct()
    {
        // Start the session
        if (! headers_sent() and session_status() == PHP_SESSION_NONE) {
            session_start([
                'name' => str_slug(config('app.name', 'angulara'), '-').'-session',
            ]);
        }
    }

    /**
     * Set Session Item.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function set($name, $value)
    {
        return $_SESSION[$name] = $value;
    }

    /**
     * Push item to existing
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function push($name, $itemValue)
    {
        if ($this->has($name) === false) {
            $this->set($name, []);

            return $_SESSION[$name][] = $itemValue;
        }

        if (is_array($this->get($name))) {
            return $_SESSION[$name][] = $itemValue;
        }

        return false;
    }

    /**
     * Set Session Item.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function has($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Check Session Item values.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function hasValue($name)
    {
        return $this->has($name) and ! empty($this->get($name));
    }

    /**
     * Merge with New data Session Item.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function merge($name, $value)
    {
        // throw an error if not exist
        if ($this->has($name) == false) {
            throw new Exception('"'.$name.'" not found in NativeSession!!');
        }

        $oldData = $_SESSION[$name];

        if (is_array($value) and is_array($oldData)) {
            return $_SESSION[$name] = array_merge($oldData, $value);
        }

        throw new Exception('NativeSession::merge() old & new values should be array!!');
    }

    /**
     * Get Session Item.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function get($name)
    {
        // throw an error if not exist
        if ($this->has($name) == false) {
            throw new Exception('"'.$name.'" not found in NativeSession!!');
        }

        return $_SESSION[$name];
    }

    /**
     * Get Session Item if Has.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function getIfHas($name)
    {
        // throw an error if not exist
        if ($this->has($name) == true) {
            return $_SESSION[$name];
        }

        return false;
    }

    /**
     * Remove Session Item.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function remove($name)
    {
        // throw an error if not exist
        if ($this->has($name) == false) {
            throw new Exception('"'.$name.'" not found in NativeSession!!');
        }

        unset($_SESSION[$name]);

        return true;
    }

    /**
     * Remove Session Item If Has.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function removeIfHas($name)
    {
        if ($this->has($name) == true) {
            return $this->remove($name);
        }

        return false;
    }

    /**
     * Get the item & remove it.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function pull($name)
    {
        $itemValue = $this->get($name);
        $this->remove($name);

        return $itemValue;
    }

    /**
     * Get the item & remove it if available.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function pullIfHas($name)
    {
        $itemValue = false;

        if ($this->has($name) == true) {
            $itemValue = $this->pull($name);
        }

        return $itemValue;
    }

    /**
     * Free all session variables.
     *
     * @return bool
     *-----------------------------------------------------------------------*/
    public function unsetAll($name)
    {
        session_unset();

        return true;
    }
}
