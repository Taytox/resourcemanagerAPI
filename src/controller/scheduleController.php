<?php
class scheduleController extends controller
{

    private function getValidationErrors(array $data, bool $new_entry = true) : array
    {
        $errors = [];
        
        #No Name entered
        if ($new_entry && empty($data["name"])){
            $errors[] = "Team Name required";
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