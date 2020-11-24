<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException as ValidationValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof TokenBlacklistedException){
            return response(['error' => 'Token can not use , get new one'],Response::HTTP_BAD_REQUEST);
        }
        elseif($exception instanceof TokenInvalidException){
            return response(['error' => 'Token innvalid'],Response::HTTP_BAD_REQUEST);
        }
        elseif($exception instanceof TokenExpiredException){
            return response(['error' => 'Token is expire'],Response::HTTP_BAD_REQUEST);
        }

        elseif($exception instanceof JWTException){
            return response(['error' => 'Token is not provided'],Response::HTTP_BAD_REQUEST);
        }

        if($exception instanceof ModelNotFoundException){
            $model = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does exist any {$model} with this specified identiticator",404);
        } 

        if($exception instanceof AuthenticationException){
            return $this->unauthenticated($request,$exception);
        }

        if($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(),409);
        }

        if($exception instanceof NotFoundHttpException){
            return $this->errorResponse('The Specified Url cannot be found',404);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('the specified method you for request is invalid',405);
        }

        if($exception instanceof ValidationValidationException){
           return $this->convertValidationExceptionToResponse($exception,$request);
        }
        if($exception instanceof BadRequestHttpException){
            return $this->errorResponse('Bad request does not exist' ,$exception->getStatusCode());
        }

       
        if($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(),$exception->getStatusCode());
        }

        if($exception instanceof QueryException){
           /* $errorCode = $exception->errorInfo[1];
            if($errorCode === 1451){
                return $this->errorResponse('Cannot remove this resource permanetly . It is related with any other resources',409);
            }*/
            //else{
                return $this->errorResponse($exception->getMessage(),500);
            //}
        }

        if(config('app.debug')){
            return parent::render($request, $exception);
        }
        return $this->errorResponse('unexcepted exception',500);
    }
}
