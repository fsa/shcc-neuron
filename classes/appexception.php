<?php
class AppException extends Exception {
    public function Handler($ex) {
        HTML::showException($ex->getMessage());
    }
}
