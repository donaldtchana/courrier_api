<?php
class Controller
{
    public function __construct(private Courrier $cour)
    {
    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        }
        if (!$id) {
            $this->getResourceCollecttion($method);
        }
    }

    // private function processResourceNamed(string $method, string $name): void
    // {
    //     $name = str_replace("%20", " ", $name);
        
    //     $product = $this->cour->getByName($name);
    //     if (!$product) {
    //         http_response_code(404); // Not found
    //         echo json_encode(["message" => "Product not found"]);
    //         return;
    //     }

    //     switch ($method) {
    //         case "GET":
    //             echo json_encode($product);
    //             break;
    //         default:
    //             http_response_code(405); // Unallowed method
    //             header("Allow: GET");
    //     }
    // }

    private function processResourceRequest(string $method, string $id): void
    {
        $product = $this->cour->get($id);

        if (!$product) {
            http_response_code(404); // Not found
            echo json_encode(["message" => "Product not found"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($product);
                break;

            case "PATCH":
                $data =  (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data, false);

                if (!empty($errors)) {
                    http_response_code(422); // Unprocessable request
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                if (empty($data)) {
                    http_response_code(304); // Not modified
                    echo json_encode(["message" => "Nothing to update"]);
                    break;
                }
                $rows = $this->cour->update($product, $data);
                echo json_encode([
                    "message" => "Courrier $id à été mis à jour",
                    "rows" => $rows
                ]);
                break;

            case "DELETE":
                $rows = $this->cour->delete($id);
                echo json_encode([
                    "message" => "Courrier $id à été supprimer",
                    "rows" => $rows
                ]);
                break;

            default:
                http_response_code(405); // Unallowed method
                header("Allow: GET, PATCH, DELETE");
        }
    }

    private function getResourceCollecttion($method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->cour->getAll());
                break;

            case "POST":
                $data =  (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    http_response_code(422); //Unprocessable request
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->cour->create($data);
                if (!$id) {
                    http_response_code(409); //Conflict
                    echo json_encode(["errors" => "A product already exist with that name"]);
                    break;
                }
                http_response_code(201); // Created
                echo json_encode([
                    "message" => "Courrier Inserer",
                    "id" => $id
                ]);
                break;

            default:
                http_response_code(405); // Unallowed method
                header("Allow: GET, POST");
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        // if ($is_new && (empty($data["type"]) || empty($data["source"]) )) {
        //     array_push($errors, "Tout les champs sont requis");
        // }

        // if (array_key_exists("prix", $data)) {
        //     if (filter_var($data["prix"], FILTER_VALIDATE_INT) === false) {
        //         array_push($errors, "Le prix doit être un entier");
        //     }
        // }
        // if (array_key_exists("quantite", $data)) {
        //     if (filter_var($data["quantite"], FILTER_VALIDATE_INT) === false) {
        //         array_push($errors, "Le quantité doit être un entier");
        //     }
        // }
        // if (array_key_exists("nom", $data)) {
        //     if (filter_var($data["nom"], FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/[a-zA-Z0-9_\\'\s]*/"]]) === false) {
        //         $errors[] = "La nom du produit est invalide";
        //     }
        // }

        return $errors;
    }
}
