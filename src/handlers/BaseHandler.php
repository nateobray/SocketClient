<?php
namespace obray\handlers;

class BaseHandler implements \obray\interfaces\SocketServerHandlerInterface
{

    private $client;
    
    public function onStart(\obray\SocketServer $connection): void
    {
        print_r("Started client\n");
        $this->client = $connection;
    }

    public function onStartClient(\obray\SocketClient $connection): void
    {
        print_r("Started client\n");
        $this->client = $connection;
    }

    public function onData(string $data, \obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("On Data\n");
        print_r($data);
    }

    public function onConnect(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("Connecting...");
        
    }

    public function onConnected(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("success\n");
        print_r("Now that you are connected, you should write something with qWrite.\n");
    }

    public function onConnectFailed(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("failed\n");
    }

    public function onWriteFailed($data, \obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("Write to socket failed\n");
    }

    public function onReadFailed(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("Read from socket failed\n");
    }

    public function onDisconnect(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("Disconnecting...");
    }

    public function onDisconnected(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        print_r("Success\n");
    }
}