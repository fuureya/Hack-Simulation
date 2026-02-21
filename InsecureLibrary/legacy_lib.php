<?php
// legacy_lib.php
// VULN A06: Library ini memiliki kerentanan PHP Object Injection yang sudah diketahui (CVE simulasi)
// Dan skrip ini mengandung fungsi berbahaya yang bisa dipanggil jika objek diserialisasi

class LegacyMailer
{
    public $template;
    public $recipient;
    public $log_file = "mail.log";

    public function __construct($recipient, $template)
    {
        $this->recipient = $recipient;
        $this->template = $template;
    }

    // Magic method __destruct yang berbahaya
    public function __destruct()
    {
        if (isset($this->template) && isset($this->log_file)) {
            // VULN: Menulis konten template ke file log tanpa validasi path
            // Attacker bisa ganti log_file jadi .php untuk RCE
            file_put_contents(__DIR__ . "/" . $this->log_file, "Sending mail to " . $this->recipient . " with content: " . $this->template . "\n", FILE_APPEND);
        }
    }
}

// Simulasi library lama lainnya yang punya XSS
function legacy_render($text)
{
    // VULN: Tidak ada sanitasi, langsung kembalikan teks
    return "<div>" . $text . "</div>";
}
