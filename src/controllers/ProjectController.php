<?php
namespace controllers;
use models\Card;
use Random\RandomException;

class ProjectController extends AppController
{
    const MAX_FILE_SIZE = 1024*1024*10;
    const SUPPORTED_TYPES = ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'];
    const UPLOAD_DIRECTORY = "/../public/uploads/";

    private $messages = [];

    /**
     * @throws RandomException
     */
    public function addProject(){
        if($this->isPost()
            && is_uploaded_file($_FILES['file']['tmp_name'])
            && $this->validate($_FILES['file'])
        ){
            move_uploaded_file(
                $_FILES['file']['tmp_name'],
                dirname(__DIR__).self::UPLOAD_DIRECTORY . $_FILES['file']['name']
            );

            $card = new Card(
                random_int(1, 999999),
                $_POST["title"],
                $_POST["description"],
                $_FILES['file']['name']
            );

            return $this->render(
                "dashboard",
                [
                    "messages" => $this->messages,
                    "cards" => [$card]
                ],
            );
        }


        return $this->render("add-project", ["messages" => $this->messages]);
    }

    private function validate(array $file):bool
    {
        if($file['size'] > self::MAX_FILE_SIZE){
            $this->messages[] = "File is too large";
            return false;
        }

        if(!isset($file['type']) || !in_array($file['type'], self::SUPPORTED_TYPES)){
            $this->messages[] = "File type is not supported";
            return false;
        }

        return true;
    }
}