<?php

/**
 * File used for handling exceptions
 *
 *
 * @package Sidejump
 * @author  Jenis Patel <jenis.patel@daffodilsw.com>
 */

/** Class for all syncrhonization exceptions.
 *
 *  @author Jenis Patel <jenis.patel@daffodilsw.com>
 *
 */
class SyncException extends Exception {

    protected $exit_code;

    /** Returns the exception's exit code.
     *
     *  @return int The exception's exit code.
     *
     */
    public function get_exit_code() {
        return $this->exit_code;
    }

}
