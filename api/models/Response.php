<?php

class Response implements JsonSerializable{

    /* Attributes */

    private $success;
    private $error;
    private $data;
    private $message;

    /* Constructor */

    public function __construct($success = false, $message = '', $data = null, $error = null) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->error = $error;
    }

    /* Setters */

    public function setSuccess($success) {
        $this->success = $success;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setError($error) {
        $this->error = $error;
    }

    /* Getters */

    public function getSuccess() {
        return $this->success;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getData() {
        return $this->data;
    }

    public function getError() {
        return $this->error;
    }

    /* Helpers */
    public function jsonSerialize() {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
            'error'   => $this->error,
        ];
    }

    /* Operations */

}
