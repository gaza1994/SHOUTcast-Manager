<?php
  namespace SHOUTcastRipper;

  require 'audio_file.php';
  require 'metadata_block.php';
  require 'http_request_message.php';
  require 'http_reponse_message.php';
  require 'http_streaming.php';

  class Ripper {
    private $metadata, $next_metadata_position, $icy_metaint, $mp3, $stream_bytes_count = 0;
    private $options, $default_options = array(
      'path'               => '.',
      'split_tracks'       => false,
      'max_track_duration' => 3600, #sec (1 h)
      'max_track_length'   => 102400000 #bytes (100 mb)
    );

    public function __construct($options=array()) {
      $this->options = array_merge($this->default_options, $options);
    }

    public function start($url) {
      $http_streaming = new HttpStreaming($url);
      $http_streaming->open();

      $response_message = $http_streaming->response_message();
      $this->icy_metaint = $response_message->icy_metaint();
      $this->next_metadata_position = $this->icy_metaint;

      $this->open_mp3file(AudioFile::default_mp3file_name());
      while ($buffer = $http_streaming->read_stream())
        if (!$this->process_received_buffer($buffer) || $this->are_limits_reached()) break;
    }

    private function are_limits_reached(){
      return $this->mp3->length() >= $this->options['max_track_length'] || $this->mp3->duration() >= $this->options['max_track_duration'];
    }

    private function open_mp3file($filename){
      $fullpath = realpath($this->options['path'])."/$filename.mp3";
      $this->mp3 = new AudioFile($fullpath);
    }

    private function metadata_block_completed(){
      if ($this->options['split_tracks']) {
        $this->open_mp3file(AudioFile::safe_filename($this->metadata->stream_title()));
      }
    }

    private function process_received_buffer($buffer){
      $buffer_len = strlen($buffer);
      $this->stream_bytes_count += $buffer_len;

      # There is still some metadata in the new buffer.
      if ($this->metadata && !$this->metadata->is_complete()){
        $remaining_len = $this->metadata->remaining_length();
        $this->metadata->write(substr($buffer, 0, $remaining_len));
        if ($this->metadata->is_complete()) {
          $this->metadata_block_completed();
          $this->mp3->write_buffer_skipping_metadata($buffer, 0, $remaining_len+1);
        }
      }
      # A new metadata block has begun
      else if ($this->icy_metaint && $this->stream_bytes_count > $this->next_metadata_position) {
        $start = $buffer_len-($this->stream_bytes_count-$this->next_metadata_position);
        $this->metadata = new MetadataBlock(ord($buffer[$start])*16);
        $end = $start+1+$this->metadata->expected_length();

        # Metadata block exists
        if ($this->metadata->expected_length() > 0){
          $this->metadata->write(($start != $buffer_len) ? substr($buffer, $start+1, $this->metadata->expected_length()) : '');
          $this->mp3->write_buffer_skipping_metadata($buffer, $start+1, $this->metadata->expected_length()+1);
          if ($this->metadata->is_complete()){
            $this->metadata_block_completed();
          }
        }
        # Metadata block is not present.
        else {
          $this->mp3->write_buffer_skipping_metadata($buffer, $start, 1);
        }

        $this->next_metadata_position = $this->next_metadata_position+$this->icy_metaint+$this->metadata->expected_length()+1;
      }
      # Buffers with only audio stream can be dumped directly.
      else{
        $this->mp3->write_buffer($buffer);
      }
      return true;
    }
  }
?>