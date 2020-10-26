<?php

namespace App\Exceptions;

use App\Exceptions\Game\GameHashException;
use App\Exceptions\Game\GameIdException;
use App\Exceptions\Game\UnallowedGameException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;

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
     * @param \Exception $exception
     *
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param            $request
     * @param \Exception $exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof GameIdException) {
            return response()->json(['error' => 'Invalid game'], 422);
        }

        if ($exception instanceof GameHashException) {
            return response()->json(['error' => 'Invalid game identifier'], 422);
        }

        if ($exception instanceof UnallowedGameException) {
            return response()->json(['error' => 'Accessing missing or unallowed game'], 422);
        }

        return parent::render($request, $exception);
    }
}
