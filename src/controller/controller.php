<?php
class controller
{
        public function __construct(protected object $gateway,protected string $collectionType)
    {

    }

public function processRequest(string $method, ?string $id,?string $modifier):void
{

        #If an ID is in the URL, process for that single item otherwise process for a full collection. 
        if ($id) {
            
            $this->processResourceRequest($method, $id, $modifier);
            
        } else {
            
            $this->processCollectionRequest($method);
            
        }

}
protected function processResourceRequest(string $method, string $id, ?string $modifier):void
    {
        
    
        $result = $this->gateway->get($id,$modifier);
        if(! $result){
            http_response_code(404);
            echo json_encode(["Message"=>"$this->collectionType not found"]);
            return;
        }

        switch($method){
            case "GET":
                echo json_encode($result);
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
                $rows = $this->gateway->update($result,$data);       
                http_response_code(201);
                echo json_encode([
                    "message" => "$this->collectionType $id updated",
                    "rows" => $rows
                ]);
            break;
            case "DELETE":
                $rows = $this->gateway -> delete($id);
                echo json_encode([
                    "message" => "$this->collectionType $id deleted",
                    "rows" => $rows
                ]);
            break;
            default:
            http_response_code(405);
            header("Allow: GET, PATCH,DELETE");
        }
    }
        protected function processCollectionRequest (String $method) : void
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
                    "message" => "$this->collectionType created",
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
        return [];

}
}