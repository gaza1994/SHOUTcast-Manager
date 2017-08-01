<?php
  namespace SHOUTcastRipper;

  class AudioFile {
    private $handle, $path, $length, $opened_at;

    /**
     * Creates a new empty file in the location specified by the $path parameter.
     * If a file with the same name exists will be overwritten.
     */
    public function __construct($path) {
      $this->path      = $path;
      $this->length    = 0;
      $this->opened_at = time();
      $this->open();
    }

    public function __destruct() {
      $this->close();
    }

    public function length() {
      return $this->length;
    }

    /**
     * Return the current file duration is seconds. This is NOT the actual duration of the audio file.
     */
    public function duration() {
      return time() - $this->opened_at;
    }

    /**
     * Writes a buffer (that represents the audio data) to the file
     * and increments the $length variable that keeps track of the total size of the file.
     */
    public function write_buffer($buffer, $start=0, $len=null) {
      $buffer = $len ? substr($buffer, $start, $len) : substr($buffer, $start);
      fwrite($this->handle, $buffer);
      $this->length += strlen($buffer);
    }

    /**
     * Writes a buffer to the file, but not all the bytes, only the real audio stream.
     */
    public function write_buffer_skipping_metadata($buffer, $meta_start, $meta_len) {
      if ($meta_start != 0)
        $this->write_buffer(substr($buffer, 0, $meta_start));
      $this->write_buffer(substr($buffer, $meta_start+$meta_len));
    }

    /**
     * Parse a string replacing all the characters that are not valid for a filename.
     * If the filename is than empty, it returns a default name that is based on the current time.
     */
    public static function safe_filename($string) {
      $name = trim(preg_replace("/[^a-zA-Z0-9_]+/", "", trim(str_replace(" ", "_", $string))));
      return strlen($name) > 0 ? $name : self::default_mp3file_name();
    }

    public static function default_mp3file_name() {
      return "untitled_".date("Ymd_his");
    }

    private function open() {
      if (!($this->handle = fopen($this->path, 'wb')))
        throw new \Exception("Unable to create file {$this->path}");
    }

    private function close() {
      if (is_resource($this->handle)) fclose($this->handle);
    }
  }
?>