<?php
namespace Src\Controller;

use Src\Model\UserModel;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *  version="1.0.0",
 *  title="OpenApi Demo",
 *  description="L5 Swagger OpenApi description",
 *  @OA\Contact(
 *  email="tigranpetrasyan@gmail.com"
 *  )
 * )
 */
class UserController {
    private $db;
    private $requestMethod;
    private $userId;
    private $user;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->user = new UserModel($db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->create();
                break;
            case 'GET':
                if ($this->userId) {
                  $response = $this->get($this->userId);
                } else {
                  $response = $this->getAll();
                };
                break;
            case 'DELETE':
                $response = $this->delete($this->userId);
                break;
            case 'PUT':
                $response = $this->update($this->userId);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    /**
     * @OA\Get(
     *  path="/user",
     *  summary="Method to read all saved users from database.",
     *  tags={"Users"},
     *  @OA\Response(response="200", description="Get All User"),
     *  @OA\Response(response="404", description="Not Found")
     * )
     *
    */
    private function getAll()
    {
        $result = $this->user->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    /**
      * @OA\Get(
      * path="/user/{id}",
      * summary="Method to get single user.",
      * tags={"Users"},
      * @OA\Parameter(
      *   name="id",
      *   in="query",
      *   required=true,
      *   description="The id passed to get data like query string",
      *   @OA\Schema(type="string")
      * ),
      * @OA\Response(response="200", description="Get User"),
      * @OA\Response(response="404", description="Not Found")
      * )
    */
    private function get($id)
    {
        $result = $this->user->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);

        return $response;
    }

    /**
     * @OA\Post(
     * path="/user",
     * summary="Method to create new user.",
     * tags={"Users"},
     * @OA\RequestBody(
     *  @OA\MediaType(
     *    mediaType="json",
     *    @OA\Schema(
     *       @OA\Property(
     *          property="firstname",
     *          type="string",
     *        ),
     *        @OA\Property(
     *          property="lastname",
     *          type="string",
     *        ),
     *        @OA\Property(
     *          property="email",
     *          type="email",
     *        ),
     *      ),
     *    ),
     * ),
     * @OA\Response(
     *   response="200",
     *   description="Save User",
     * ),
     * @OA\Response(response="404", description="Not Found")
     * )
     */
    private function create()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validate($input)) {
            return $this->unprocessableEntityResponse();
        }

        $this->user->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;

        return $response;
    }

    /**
     * @OA\Put(
     * path="/user/{id}",
     * summary="Method to update user.",
     * tags={"Users"},
     * @OA\RequestBody(
     *  @OA\MediaType(
     *    mediaType="json",
     *    @OA\Schema(
     *       @OA\Property(
     *          property="id",
     *          type="integer",
     *        ),
     *       @OA\Property(
     *          property="firstname",
     *          type="string",
     *        ),
     *        @OA\Property(
     *          property="lastname",
     *          type="string",
     *        ),
     *        @OA\Property(
     *          property="email",
     *          type="email",
     *        ),
     *      ),
     *    ),
     * ),
     * @OA\Response(
     *   response="200",
     *   description="Update User",
     * ),
     * @OA\Response(response="404", description="Not Found")
     * )
     */
    private function update($id)
    {
        $result = $this->user->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validate($input)) {
            return $this->unprocessableEntityResponse();
        }

        $this->user->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }

    /**
     * @OA\Delete(
     * path="/user/{id}",
     * summary="Method to delete user from database.",
     * tags={"Users"},
     * @OA\RequestBody(
     *  @OA\MediaType(
     *    mediaType="json",
     *    @OA\Schema(
     *       @OA\Property(
     *          property="id",
     *          type="integer",
     *        ),
     *      ),
     *    ),
     * ),
     * @OA\Response(response="200", description="Delete User"),
     * @OA\Response(response="404", description="Not Found")
     * )
     */
    private function delete($id)
    {
        $result = $this->user->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }

        $this->user->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;

        return $response;
    }

    private function validate($input)
    {
        if (! isset($input['firstname'])) {
            return false;
        }

        if (! isset($input['lastname'])) {
            return false;
        }

        return true;
    }

    private function notFoundResponse()
    {
      $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
      $response['body'] = null;

      return $response;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);

        return $response;
    }
}
