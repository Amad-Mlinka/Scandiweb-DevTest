<?php

class Response implements JsonSerializable {

    /* Attributes */

    private $success;
    private $errors;
    private $data;
    private $message;

    /* Constructor */

    public function __construct($success = false, $message = '', $data = null, $errors = []) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
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
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }

    public function setErrors(array $errors) {
        $this->errors = $errors;
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

    public function getErrors() {
        return $this->errors;
    }

    /* Helpers */
    public function jsonSerialize() {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
            'errors'  => $this->errors,
        ];
    }

    /* Operations */

}
