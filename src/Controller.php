<?php

namespace HnrAzevedo\Router;

use HnrAzevedo\Validator\Validator;
use Exception;

class Controller{
    use Helper;

    protected array $fail = [];

    private function checkMethod(string $method): void
    {
        if(!method_exists($this,$method)){
            throw new Exception("{$method} not found in ".get_class($this).".");
        }
    }

    public function method($data): bool
    {        
        $this->ValidateData();

        if($this->checkFailData()){
            return false;
        }

        $method = $this->getData()['POST']['role'];

        $this->checkMethod($method);

        $this->$method($data);

        return true;
    }

    private function ValidateData(): void
    {
        $valid = Validator::execute($this->getData()['POST']);

        if(!$valid){
            foreach(Validator::getErrors() as $err => $message){
                $this->fail[] = [
                    'input' => array_keys($message)[0],
                    'message' => $message[array_keys($message)[0]]
                ]; 
            }
        }
    }

    private function checkFailData(): bool
    {
        if(count($this->fail) > 0 ){
            echo json_encode([
                'error'=> $this->fail
            ]);
        }
        return (count($this->fail) > 0 );
    }

}