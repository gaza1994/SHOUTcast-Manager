<?php
  namespace SHOUTcastRipper;

  class HttpStreaming {
    private $socket, $address, $port, $url, $response_message;
    const READ_BUFFER_LEN = 2048;
    const CONNECT_TIMEOUT = 10;

    public function __construct($url) {
      $this->url = $url;
      $this->parse_url();
    }

    public function __destruct() {
      $this->close();
    }

    public function response_message() {
      return $this->response_message;
    }

    /**
     * Opens a socket to the given $address and sends the http request message,
     * the server will reply with an http response message and a continuous flow of bytes
     * that represent the audio data.
     */
    public function open() {
      $this->close();
      if (!($this->socket = fsockopen($this->address, $this->port, $errno, $errstr, self::CONNECT_TIMEOUT)))
        throw new \Exception("fsockopen() return error $errno: $errstr");
      $this->response_message = new HttpResponseMessage();
      $this->send_request_message();
      $this->read_response_message();
    }

    /**
     * Return a buffer of audio data readed from the socket.
     * The audio data can contains metadata.
     */
    public function read_stream() {
      return ($this->response_message->contains_audio_data()) ? $this->response_message->remove_tail_audio_data() : $this->read();
    }


    public function close() {
      if (is_resource($this->socket)) fclose($this->socket);
    }

    private function read() {
      return fread($this->socket, self::READ_BUFFER_LEN);
    }

    /**
     * Recursively reads data from the socket until the http response message is totally received.
     */
    private function read_response_message() {
      $buffer = $this->read();
      if (!$this->response_message->is_complete()) $this->response_message->write($buffer);
      if ($this->response_message->is_complete()) return;
      $this->read_response_message();
    }

    private function send_request_message() {
      fputs($this->socket, $this->request_message());
    }

    /**
     * Gets the http request message; if it contains the non-standard header "Icy-MetaData"
     * the SHOUTcast server will reply with the audio stream and a metadata block containing the current
     * stream title and other infos.
     */
    private function request_message() {
      $request = new HttpRequestMessage($this->url, array('Icy-MetaData' => 1));
      return $request->content();
    }

    private function parse_url() {
      $url_parts = parse_url($this->url);
      if (!isset($url_parts["scheme"]) || strtolower($url_parts["scheme"]) != "http")
        throw new \Exception("Invalid url scheme");
      $this->address = $url_parts["host"];
      $this->port    = $url_parts["port"];
    }
  }
?>