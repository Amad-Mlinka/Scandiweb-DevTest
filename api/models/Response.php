<?php

class Response implements JsonSerializable {

    /* Attributes */

    private $success;
    private $errors;
    private $data;
    private $message;

    /* Constructor */

    /**
     * Constructor for the Response class.
     * @param bool $success Indicates whether the response was successful.
     * @param string $message A message describing the response.
     * @param mixed $data The data to include in the response, if any.
     * @param array $errors An array of errors, if any.
     */
    
    public function __construct($success = false, $message = '', $data = null, $errors = []) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
    }

    /* Setters */

    /**
     * Sets the success status of the response.
     * @param bool $success Indicates whether the response was successful.
     */

    public function setSuccess($success) {
        $this->success = $success;
    }

    /**
     * Sets the message for the response.
     * @param string $message The message to set.
     */

    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * Sets the data for the response.
     * @param mixed $data The data to set.
     */

    public function setData($data) {
        $this->data = $data;
    }

    /**
     * Adds an error or errors to the response.
     * @param mixed $error A single error or an array of errors to add.
     */

    public function setError($error) {
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }

    /**
     * Sets the errors for the response.
     * @param array $errors An array of errors to set.
     */

    public function setErrors(array $errors) {
        $this->errors = $errors;
    }

    /* Getters */

    /**
     * Gets the success status of the response.
     * @return bool Indicates whether the response was successful.
     */

    public function getSuccess() {
        return $this->success;
    }

    /**
     * Gets the message of the response.
     * @return string The message describing the response.
     */

    public function getMessage() {
        return $this->message;
    }

    /**
     * Gets the data included in the response.
     * @return mixed The data included in the response.
     */

    public function getData() {
        return $this->data;
    }

    /**
     * Gets the errors included in the response.
     * @return array An array of errors.
     */

    public function getErrors() {
        return $this->errors;
    }

    /* Helpers */

    /**
     * Serializes the response to a JSON-compatible format.
     * @return array The data to be serialized.
     */

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
?>
