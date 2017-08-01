<?php
  namespace SHOUTcastRipper;

  class MetadataBlock {
    private $expected_length, $content;

    /**
     * Creates a new empty MetadataBlock with the given $expected_length.
     * The actual content can be added using the write() method.
     */
    public function __construct($expected_length) {
      $this->content = '';
      $this->expected_length = $expected_length;
    }

    public function expected_length() {
      return $this->expected_length;
    }

    public function write($buffer) {
      $this->content .= $buffer;
    }

    public function content() {
      return $this->content;
    }

    public function length() {
      return strlen($this->content);
    }

    /**
     * Determines if the metadata block is complete. A metadata block is completed
     * when its content has at least $expected_length bytes of data.
     */
    public function is_complete() {
      return $this->remaining_length() == 0;
    }

    public function remaining_length() {
      return $this->expected_length - $this->length();
    }

    /**
     * Gets the title of the song/track.
     * For example, if the metadata is "StreamTitle='Ari Shine - Crank It Out!';StreamUrl='http://digitalia.fm'"
     * this method will return the string "Ari Shine - Crank It Out!".
     */
    public function stream_title() {
      if (!$this->is_complete())
        throw new \Exception("The metadata block is not complete yet");
      $start = strlen("StreamTitle=");
      $end = strpos($this->content, ";", $start);
      return substr($this->content, $start, $end-$start);
    }
  }
?>