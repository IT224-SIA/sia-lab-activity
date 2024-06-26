<?php

namespace App\Exceptions;

use App\Traits\APIResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Auth\Access\AuthorizationException;
//use Illuminate\Auth\AuthenticationException; // Import AuthenticationException
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    //use APIResponser;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */

    public function render($request, Throwable $exception)
    {
        // http not found
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $message = Response::$statusTexts[$code];

            //return $this->errorResponse($message, $code);

            return response()->json([
                'error' => $message,
                'code' => $code
            ], $code);
        }

        // instance not found
        if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));

            //return $this->errorResponse("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);

            return response()->json([
                'error' => "Does not exist any instance of {$model} with the given id",
                'code' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        // validation exception
        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();

            //return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->json([
                'errors' => $errors,
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // access to forbidden
        if ($exception instanceof AuthorizationException) {
            //return $this->errorResponse($exception->getMessage(), Response::HTTP_FORBIDDEN);

            return response()->json([
                'error' => $exception->getMessage(),
                'code' => Response::HTTP_FORBIDDEN
            ], Response::HTTP_FORBIDDEN);
        }

        // unauthorized access
        if ($exception instanceof AuthenticationException) {
            //return $this->errorResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);

            return response()->json([
                'error' => $exception->getMessage(),
                'code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        // if you are running in development environment
        if (env('APP_DEBUG', false)) {
            return parent::render($request, $exception);
        }

        //return $this->errorResponse('Unexpected error. Try later', Response::HTTP_INTERNAL_SERVER_ERROR);

        return response()->json([
            'error' => 'Unexpected error. Try later',
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

        if ($exception instanceof ClientException) {
            $message = $exception->getResponse()->getBody();
            $code = $exception->getCode();
            return $this->errorMessage($message,200);
        }
           
    }

}


