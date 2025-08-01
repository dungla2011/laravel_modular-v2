<?php

namespace Modules\Base\Exceptions;

use Exception;

class ModuleException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): bool
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
                'code'    => $this->getCode(),
            ], 400);
        }

        return response()->view('errors.module', [
            'message' => $this->getMessage(),
        ], 400);
    }
}
