<?php
  namespace SHOUTcastRipper;

  class HttpRequestMessage {
    private $content='';
    const CRLF = "\r\n";

    public function __construct($url, $headers=array()) {
      $url_parts = parse_url($url);
      $this->add_line("GET ".(isset($url_parts["path"]) ? $url_parts["path"] : '/')." HTTP/1.1");
      $this->add_header("Host", "{$url_parts['host']}:{$url_parts['port']}");
      $this->add_header("User-Agent", "PHP");
      $this->add_header("Accept", "*/*");
      foreach ($headers as $key => $value)
        $this->add_header($key, $value);
      $this->add_line("");
    }

    public function content() {
      return $this->content;
    }

    private function add_header($key, $value) {
      $this->add_line("$key: $value");
    }

    private function add_line($text){
      $this->content .= $text.self::CRLF;
    }
  }
?>