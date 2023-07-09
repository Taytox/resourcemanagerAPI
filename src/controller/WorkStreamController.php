<?php
class workstreamController
{


    public function __construct(private WorkstreamGateway $gateway)
    {



    }
    public function processRequest(string $method, ?string $id):void 
    {

        #If an ID is in the URL, process for that single item otherwise process for a full collection. 
        if ($id) {
            
           # $this->processResourceRequest($method, $id);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }
    }


    private function processCollectionRequest (String $method) : void
    {
        switch ($method) {
            case "GET" : 
                echo json_encode($this->gateway->getAll());
                break;
        }

    }
    
}