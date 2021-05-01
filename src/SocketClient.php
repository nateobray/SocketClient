<?php

namespace obray;

class SocketClient
{
    const SELECT = 0;
    const EV = 1;

    // connection details
    private $protocol;
    private $host;
    private $port;
    private $context;
    private $socket;
    private $socketConnection;

    // internal
    private $eventLoopType;
    private $socketWatcher;
    private $connections = [];

    // store handler
    private $handler = NULL;

    // parallel
    private $pool;

    public function getHost()
    {
        return $this->host;
    }

    /**
     * Constructor
     * 
     * Takes the necessary data to start a connection and stores it on the sever object to be used when running
     * start.
     */

    public function __construct(string $protocol='tcp', string $host='localhost', int $port=8080, \obray\StreamContext $context=NULL)
    {
        $this->protocol = $protocol;
        $this->host = $host;
        $this->port = $port;
        $this->context = $context;
        if($this->context == NULL){
            $this->context = new \obray\StreamContext();
        }
    }

    /**
     * Start
     * 
     * Starts the socket server by attempt to bind on the host and port specified.  If successfull it start the
     * stream select loop to and handle incoming and outgoing data
     */

    public function connect(\obray\interfaces\SocketServerHandlerInterface $handler)
    {
        $this->handler = $handler;
        
        // determine which event loop to use
        if( $this->eventLoopType === NULL && !class_exists( '\EV' || $this->eventLoopType === EV ) ) {
            $this->eventLoop = new \obray\eventLoops\EVLoop();
        } else {
            $this->eventLoop = new \obray\eventLoops\StreamSelectEventLoop($this->socket);
        }

        // start watching connections
        $this->watch();
    }

    /**
     * Watch
     * 
     * Starts watch for network activity on main socket
     */

    private function watch()
    {
        // create new event loop
        $this->eventLoop = new \obray\eventLoops\EVLoop();
        
        // add watcher for cleaning up disconnected connections from the main connection list
        $this->disconnectWatcher = $this->eventLoop->watchTimer(0, 3, function($watcher){
            if(empty($this->socketConnection) || $this->socketConnection->isConnected() === false){
                
                $this->handler->onStartClient($this);
                $address = $this->protocol."://".$this->host.":".$this->port;
                print_r("Connecting: " . $address . "\n");
                $this->socket = stream_socket_client ( $address , $this->errorNo , $this->errorMessage, 30, STREAM_CLIENT_ASYNC_CONNECT, $this->context->get());
                if( !is_resource($this->socket) ){
                    throw new \Exception("Unable to connect to ".$this->host.":".$this->port." over ".$this->protocol.": " . $this->errorMessage . "\n");
                }
                print_r("Connected to ".$this->host.":".$this->port." over ".$this->protocol."\n");

                $this->socketConnection = new \obray\SocketConnection($this->socket, $this->eventLoop, $this->handler, $this->context->isEncrypted(), false);
                $this->socketConnection->run();
            }
            
        }, $this->socket);
        // run the event loop
        $this->eventLoop->run();
    }

    /**
     * Set Select Timeout
     * 
     * This sets the select timeout.  A smaller number make se the server process requests in shorter
     * intervals, but also comsumes more CPU.  It's not recommended to set this to 0.
     */

    public function setEventLoopType(int $eventLoopType)
    {
        $this->eventLoopType = $eventLoopType;
    }

}
