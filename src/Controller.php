<?php
class Controller
{
    /**
     * Constructs a new instance of the class.
     *
     * @param Courrier $cour The Courrier object.
     */
    public function __construct(private Courrier $cour)
    {
    }
    /**
     * Process a request.
     *
     * @param string $method The HTTP method of the request.
     * @param string|null $id The id of the resource to process.
     * @param string|null $path The path of the resource to process.
     * @throws Some_Exception_Class A description of the exception.
     * @return void
     */
    public function processRequest(string $method, ?string $id, ?string $path): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        }
        if ($path) {
            $this->getResourceCollection($method, $path);
        }
        if (!$id && !$path) {
            $this->getResourceCollection($method);
        }
    }

    /**
     * Process a resource request.
     *
     * @param string $method The HTTP method of the request.
     * @param string $id The ID of the resource.
     * @throws None
     * @return void
     */
    private function processResourceRequest(string $method, string $id): void
    {
        $courrier = $this->cour->get($id);

        if (!$courrier) {
            http_response_code(404);
            echo json_encode(["message" => "Courrier not found"]);
            return;
        }

        switch ($method) {
            case "GET":
                echo json_encode($courrier);
                break;

            case "PATCH":
                $data =  (array) json_decode(file_get_contents("php://input"), true);
                $errors = $this->getValidationErrors($data, false);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                if (empty($data)) {
                    http_response_code(304);
                    echo json_encode(["message" => "Nothing to update"]);
                    break;
                }

                $rows = $this->cour->update($courrier, $data);

                if ($rows === false) {
                    echo json_encode(["message" => "Le courrier ne peut pas être modifier"]);
                    break;
                }

                echo json_encode([
                    "message" => "Courrier $id à été mis à jour",
                    "rows" => $rows
                ]);
                break;

            case "DELETE":
                $rows = $this->cour->delete($id);
                if ($rows === false) {
                    echo json_encode([
                        "message" => "Le courrier n'as pas été supprimer"
                    ]);
                    break;
                }
                echo json_encode([
                    "message" => "Courrier $id à été supprimer",
                    "rows" => $rows
                ]);
                break;

            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }

    /**
     * Handles the resource collection based on the given method and path.
     *
     * @param string $method The HTTP method.
     * @param string|null $path The resource path.
     * @return void
     */
    private function getResourceCollection(string $method, ?string $path = ""): void
    {
        switch ($method) {
            case "GET":
                if ($path === '') {
                    echo json_encode($this->cour->getAll());
                    break;
                }
                http_response_code(404);
                echo json_encode(["errors" => "Path not found"]);
                break;

            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    http_response_code(422); // Unprocessable request
                    echo json_encode(["errors" => $errors]);
                    break;
                }

                $id = $this->cour->create($data);
                if (!$id) {
                    http_response_code(409); // Conflict
                    echo json_encode(["errors" => "A courrier already exists with that name"]);
                    break;
                }
                http_response_code(201); // Created
                echo json_encode([
                    "message" => "Courrier Inserted",
                    "id" => $id
                ]);
                break;
            default:
                http_response_code(405); // Method not allowed
                header("Allow: GET, POST");
        }
    }

    /**
     * Retrieves validation errors from the given data array.
     *
     * @param array $data The data array to validate.
     * @param bool $is_new Whether the data is for a new entry.
     * @return array The array of validation errors.
     */
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if (
            $is_new &&
            (trim($data["InOutCourier"]) == "" ||
                trim($data["ReferenceCourier"]) == "" ||
                trim($data["Destinataire"]) == "" ||
                trim($data["ObjetCourier"]) == "" ||
                trim($data["SourceCourier"]) == ""
            )
        ) {
            $errors[] = 'les information sont requis';
        }

        if (
            $is_new &&
            (empty(trim($data["InOutCourier"])) ||
                empty(trim($data["SourceCourier"])) ||
                empty(trim($data["Destinataire"]) ||
                    empty(trim($data["ReferenceCourier"])) ||
                    empty(trim($data["ObjetCourier"])))
            )
        ) {
            $errors[] = "Tout les champs sont requis";
        }

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
