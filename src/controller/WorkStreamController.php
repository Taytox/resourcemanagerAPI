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
            
            $this->processResourceRequest($method, $id);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }
    }


    private function processResourceRequest(string $method, string $id):void
    {
        $workstream = $this->gateway->get($id);
        if(! $workstream){
            http_response_code(404);
            echo json_encode(["Message"=> "Workstream not found"]);
            return;
        }

        switch($method){
            case "GET":
                echo json_encode($workstream);
            break;

            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"),true);
                
                
                #Carry out input validation
                $errors = $this->getValidationErrors($data, false);
                if (! empty($errors)){
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $rows = $this->gateway->update($workstream,$data);       
                http_response_code(201);
                echo json_encode([
                    "message" => "Workstream $id updated",
                    "rows" => $rows
                ]);
            break;
            case "DELETE":
                $rows = $this->gateway -> delete($id);
                echo json_encode([
                    "message" => "Workstream $id deleted",
                    "rows" => $rows
                ]);
            break;
            default:
            http_response_code(405);
            header("Allow: GET, PATCH,DELETE");
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
            default:
                http_response_code(405);
                header("Allow: GET, POST");
            }

    }

    private function getValidationErrors(array $data, bool $new_entry = true) : array
    {
        $errors = [];
        
        #No Name entered
        if ($new_entry && empty($data["name"])){
            $errors[] = "Workstream Name required";
        }
        #No location entered
        if ($new_entry && empty($data["location"])){
            $errors[] = "location ID required";
            
        }#Location is not an Int representing a location ID
        elseif(filter_var($data["location"], FILTER_VALIDATE_INT)===false){
                $errors[] = "location ID must be an integer. ";
        }    
    
        #No required staffing set
       # if ($new_entry && empty($data["reqstaffing"])){
         #  $errors[] = "Required Staffing Must be set";
            
      #  }#required number of staff is not an Int 
      #  elseif(filter_var($data["reqstaffing"], FILTER_VALIDATE_INT)===false){
      #          $errors[] = "Required Staffing must be an integer. ";
      #  }    

        return $errors;


    }
    
}