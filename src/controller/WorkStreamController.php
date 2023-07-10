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
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"),true);
                
                
                #Carry out input validation
                $errors = $this->getValidationErrors($data);
                if (! empty($errors)){
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }


                $id = $this->gateway->create($data);       
                http_response_code(201);
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id
                ]);
              break;
            
            
            }

    }

    private function getValidationErrors(array $data) : array
    {
        $errors = [];
        
        #No Name entered
        if (empty($data["name"])){
            $errors[] = "Workstream Name required";
        }
        #No location entered
        if (empty($data["location"])){
            $errors[] = "location ID required";
            
        }#Location is not an Int representing a location ID
        elseif(filter_var($data["location"], FILTER_VALIDATE_INT)===false){
                $errors[] = "location ID must be an integer. ";
        }    
    
        #No required staffing set
        if (empty($data["reqstaffing"])){
            $errors[] = "Required Staffing Must be set";
            
        }#required number of staff is not an Int representing a location ID
        elseif(filter_var($data["reqstaffing"], FILTER_VALIDATE_INT)===false){
                $errors[] = "Required Staffing must be an integer. ";
        }    

        return $errors;


    }
    
}