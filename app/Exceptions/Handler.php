<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\ModelNotDefined;

class Handler extends ExceptionHandler
{
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
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {   
        if ($exception instanceof AuthorizationException && $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    'message' => 'Você não está autorizado a acessar este recurso'
                ]
            ], 403);
        }

        if ($exception instanceof ModelNotFoundException && $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    'message' => 'Este recurso não existe'
                ]
            ], 403);
        }

        if ($exception instanceof ModelNotDefined && $request->expectsJson()) {
            return response()->json([
                'errors' => [
                    'message' => 'Model nao definido'
                ]
            ], 500);
        }

        return parent::render($request, $exception);
    }
}
