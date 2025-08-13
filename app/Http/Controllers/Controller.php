<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Sooqnaa API Documentation",
 *     version="1.0.0",
 *     description="Complete API documentation for the Sooqnaa marketplace platform",
 *     @OA\Contact(
 *         email="support@sooqnaa.com",
 *         name="Sooqnaa Support Team"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Sooqnaa API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token to access protected endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication and authorization endpoints"
 * )
 *
 * @OA\Tag(
 *     name="Users",
 *     description="User management and profile operations"
 * )
 *
 * @OA\Tag(
 *     name="Health",
 *     description="System health and monitoring endpoints"
 * )
 */
abstract class Controller
{
    //
}