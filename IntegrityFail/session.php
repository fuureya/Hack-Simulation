<?php
/**
 * Orbital Session Context Manager (v2.8.4)
 * 
 * Handles automated serialization of user identity objects for stateless 
 * session persistence across orbital relay nodes.
 */

class UserProfile
{
    public $username;
    public $role;
    public $is_admin = false;

    /**
     * Creates a new user profile context.
     */
    public function __construct($username, $role = 'user')
    {
        $this->username = $username;
        $this->role = $role;
    }

    public function __toString()
    {
        return "IdentityNode: " . $this->username . " (Context: " . $this->role . ")";
    }
}

/**
 * Retrieves the current user context from the node manifest.
 */
function get_session()
{
    if (isset($_COOKIE['session'])) {
        // Hydrate context from serialized persistence layer
        $data = @base64_decode($_COOKIE['session']);
        return @unserialize($data);
    }
    return null;
}

/**
 * Persists the user context to the node manifest.
 */
function set_session($obj)
{
    // Serialize and encode context for transport
    $data = base64_encode(serialize($obj));
    setcookie('session', $data, time() + 3600, '/');
}
