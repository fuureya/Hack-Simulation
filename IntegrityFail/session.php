<?php
// session.php
// VULN A08: Aplikasi menyimpan session data dalam bentuk base64 serialized object di Cookie
// Tanpa adanya tanda tangan digital (HMAC), attacker bisa memanipulasi konten object.

class UserProfile
{
    public $username;
    public $role;
    public $is_admin = false;

    public function __construct($username, $role = 'user')
    {
        $this->username = $username;
        $this->role = $role;
    }

    public function __toString()
    {
        return "User: " . $this->username . " (" . $this->role . ")";
    }
}

function get_session()
{
    if (isset($_COOKIE['session'])) {
        // VULN: Base64 decode lalu unserialize tanpa verifikasi integritas!
        $data = base64_decode($_COOKIE['session']);
        return unserialize($data);
    }
    return null;
}

function set_session($obj)
{
    $data = base64_encode(serialize($obj));
    setcookie('session', $data, time() + 3600, '/');
}
