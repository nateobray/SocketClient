<?php
namespace obray\handlers;

class WebSocketHandler extends \obray\base\SocketServerBaseHandler
{
    private $isUpgraded = false;
    private $acceptToken;
    private $websocket;

    public function onDisconnected(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        $this->isUpgraded = false;
    }

    public function onConnected(\obray\interfaces\SocketConnectionInterface $connection): void
    {
        
        $request = new \obray\http\Transport('GET', '/', 'HTTP/1.1', new \obray\http\Headers([
            "Connection" => "Upgrade",
            "Upgrade" => "websocket",
            "Sec-WebSocket-Key" => "x3JJHMbDL1EzLkh9GBhXDw==",
            "Sec-WebSocket-Version" => '13',
            "Sec-WebSocket-Extensions" => ''
        ]));
        $connection->qWrite($request->encode());
        
    }

    public function onData(string $data, \obray\interfaces\SocketConnectionInterface $connection): void
    {
        
        if(!$this->isUpgraded){
            
            $this->isUpgraded = true;
            $request = \obray\http\Transport::decode($data);
            $this->acceptToken = $request->getHeaders('Sec-WebSocket-Accept');
            $obj = new \stdClass();
            $obj->name = 'malouf-logan';

            $this->websocket = new \obray\WebSocket();
            $data = \obray\WebSocketFrame::encode(json_encode($obj));
            $connection->qWrite($data);
            return;
        }

        $data = $this->websocket->decode($data, $connection, [$this, 'onMessage']);
        
    }

    public function onMessage(int $opcode, string $msg, \obray\interfaces\SocketConnectionInterface $connection)
	{
		switch($opcode) {
			case \obray\WebSocketFrame::TEXT:
				break;
			case \obray\WebSocketFrame::BINARY:
				
				break;
			case \obray\WebSocketFrame::CLOSE:
				
				break;
			case \obray\WebSocketFrame::PING:
				
				break;
			case \obray\WebSocketFrame::PONG:
				
				break;
		}
	}
}