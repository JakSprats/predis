<?php
namespace Predis;

// If you want to use this class to handle TCP connections instead of the 
// default Predis\TcpConnection class, you must first register it before 
// creating any instance of Predis\Client, like in the following example:
//
// Predis\ConnectionFactory::register('tcp', '\Predis\SocketBasedTcpConnection');
// $redis = new Predis\Client('tcp://127.0.0.1');
//

class SocketBasedTcpConnection extends TcpConnection {
    protected function createResource() {
        $this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!is_resource($this->_socket)) {
            $this->emitSocketError();
        }

        // TODO: handle async, persistent, and timeout options
        // $this->_params->connection_async
        // $this->_params->connection_persistent
        // $this->_params->connection_timeout
        // $this->_params->read_write_timeout

        $remote_host = $this->_params->host;
        $remote_port = $this->_params->port;
        $remote_addr = null; // derived below

        $remote_addr_long = ip2long($remote_host);
        if ($remote_addr_long == -1 || $remote_addr_long === false) {
            $remote_addr = gethostbyname($remote_host);
        } else {
            $remote_addr = $remote_host;
        }

        if (@socket_connect($this->_socket, $remote_addr, $remote_port) === false) {
            $this->_socket = null;
            $this->emitSocketError();
        }

        if (!socket_set_block($this->_socket)) {
            $this->emitSocketError();
        }

        // Disable the Nagle algorithm
        if (!socket_set_option($this->_socket, SOL_TCP, TCP_NODELAY, 1)) {
            $this->emitSocketError();
        }

        if (!socket_set_option($this->_socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            $this->emitSocketError();
        }
    }

    public function disconnect() {
        if ($this->isConnected()) {
            // TODO: ponder socket_shutdown()
            // TODO: ponder linger socket options
            socket_close($this->_socket);
            $this->_socket = null;
        }
    }

    private function emitSocketError() {
        $errno = socket_last_error();
        $errstr = socket_strerror($errno);
        $this->onCommunicationException(trim($errstr), $errno);
    }

    public function writeBytes($value) {
        $socket = $this->getSocket();
        while (($length = strlen($value)) > 0) {
            $written = socket_write($socket, $value, $length);
            if ($length === $written) {
                return true;
            }
            if ($written === false) {
                $this->onCommunicationException('Error while writing bytes to the server');
            }
            $value = substr($value, $written);
        }
        return true;
    }

    public function readBytes($length) {
        if ($length == 0) {
            throw new \InvalidArgumentException('Length parameter must be greater than 0');
        }
        $socket = $this->getSocket();
        $value  = '';
        do {
            $chunk = socket_read($socket, $length, PHP_BINARY_READ);
            if ($chunk === false) {
                $this->onCommunicationException('Error while reading bytes from the server');
            } else if ($chunk === '') {
                $this->onCommunicationException('Unexpected empty result while reading bytes from the server');
            }
            $value .= $chunk;
        }
        while (($length -= strlen($chunk)) > 0);
        return $value;
    }

    public function readLine() {
        $socket = $this->getSocket();
        $value  = '';
        do {
            $chunk_len = 4096;
            // peek ahead (look for Predis\Protocol::NEWLINE)
            $chunk = '';
            $chunk_res = socket_recv($socket, $chunk, $chunk_len, MSG_PEEK);
            if ($chunk_res === false) {
                $this->onCommunicationException('Error while peeking line from the server');
            } else if ($chunk === '' || is_null($chunk)) {
                $this->onCommunicationException('Unexpected empty result while peeking line from the server');
            }
            if (($newline_pos = strpos($chunk, Protocol::NEWLINE)) !== false) {
                $chunk_len = $newline_pos + 2;
            }
            // actual recv (with possibly adjusted chunk_len)
            $chunk = '';
            $chunk_res = socket_recv($socket, $chunk, $chunk_len, 0);
            if ($chunk_res === false) {
                $this->onCommunicationException('Error while reading line from the server');
            } else if ($chunk === '' || is_null($chunk)) {
                $this->onCommunicationException('Unexpected empty result while reading line from the server');
            }
            $value .= $chunk;
        }
        while (substr($value, -2) !== Protocol::NEWLINE);
        return substr($value, 0, -2);
    }
}
?>
