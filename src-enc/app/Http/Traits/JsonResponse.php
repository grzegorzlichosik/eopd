<?php

namespace App\Http\Traits;

use App\Models\AppOption;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;

trait JsonResponse
{

    private function response(array $data, int $status = 200): \Illuminate\Http\JsonResponse
    {
        $response = [
            'status' => $data['status'] ?: 'success',
            'callTime' => isset($data['callTime']) ? $data['callTime'] : null,
        ];

        if (!empty($data['data'])) {
            $response['data'] = $data['data'];
        }

        if (!empty($data['message'])) {
            $response['message'] = $data['message'];
        }

        return response()->json($response, $status);
    }

    public function success(array $data): \Illuminate\Http\JsonResponse
    {
        return $this->response(array_merge($data, ['status' => 'success']));
    }


    public function error(array $data, int $status = 400, bool $frontendRedirect = false): \Illuminate\Http\JsonResponse
    {
        return $this->response(
            array_merge($data, ['status' => 'error', 'frontendRedirect' => $frontendRedirect]),
            $status
        );
    }

    /**
     *
     * @codeCoverageIgnore
     */
    public function respond(Closure $callback, int $status = 400): \Illuminate\Http\JsonResponse
    {
        $data = [];
        $data['callTime'] = -microtime(true);

        try {
            $data['data'] = $callback();
        } catch (Exception $e) {

            /**
             * If exception comes as `ExceptionWithRedirect`, set `$requiresFrontendRedirect` as true
             * and set chained exception as an original one (`$e->getPrevious()`):
             */
            if (get_class($e) === 'App\Exceptions\ExceptionWithRedirect') {
                $requiresFrontendRedirect = true;
                $e = $e->getPrevious();
            }

            /**
             * If Exception code error is 0 (default exception code), use 400 by default,
             * otherwise use Exception's code:
             * Note! Exception code can be overridden in switch() function after each case also.
             */
            $status = $this->getStatus($e, $status);

            /**
             * Get the exception error message.
             */
            $message = $this->getMessage($e);


            /**
             * If status is not set here, we will use the default status code from the exception and we will print out
             * the `$e->getMessage()` error message. In some cases, we have to hide the error message on the PRODUCTION
             * environment, so we are overriding the error message with the "safe" message for some cases here:
             *
             * The custom error cases includes the message and code (App\Exceptions) and safe to print the error code!
             */
            switch (get_class($e)) {
                // Add only those cases that includes the sensitive data and needs to be hidden in the PRODUCTION here:
                case 'Illuminate\Database\QueryException' :
                    $errorLabel = 'e_application_uncaught_error';
                    $status     = 500;
                    break;
                case 'Threadable\StateMachine\Exceptions\IllegalStateTransitionException' :
                    $errorLabel = 'e_tsm_error';
                    break;
                case 'Illuminate\Database\Eloquent\ModelNotFoundException':
                    $errorLabel = 'e_error_retrieving_data';
                    $status     = 404;
                    break;
                case 'Illuminate\Auth\AuthenticationException':
                case 'Illuminate\Validation\UnauthorizedException':
                    $errorLabel = 'e_unauthorized';
                    $status     = 401;
                    break;
                case 'Illuminate\Auth\Access\AuthorizationException':
                    $errorLabel = 'e_forbidden';
                    $status     = 403;
                    break;
                default:
                    /**
                     * By the default, we want to print out the exception errors and not overwrite the exception code.
                     */
                    $errorLabel = $message;
            }

            //We need at least to write the error to our laravel's log
            try {
                if ($status == 500) {
                    @Log::error($e);
                } else {
                    @Log::warning($e);
                }
            } catch (Exception $extraExceptional) { /* DO NOTHING */
            }

            /**
             * The response should always have the same label whether its dev or production:
             */
            $data['message'] = $errorLabel;

            /**
             * For dev environments, include the full error message:
             */
            if (config('app.env') == 'dev') {
                $data['debug_error_message'] = $e->getMessage();
                $data['debug_error_trace']   = $e->getTrace();
            }

            /**
             * Print to the logs anytime
             */
            Log::warning("JsonResponse exception message: " . $e->getMessage());
            Log::warning("JsonResponse exception trace: ", $e->getTrace());


            $data['callTime'] += microtime(true);
            return $this->error($data, $status, ($requiresFrontendRedirect ?? false));

        }

        $data['callTime'] += microtime(true);
        return $this->success($data);
    }

    private function getStatus(Exception $e, int $status): int
    {
        return intval($e->getCode()) === 0 ? $status : intval($e->getCode());
    }

    private function getMessage(Exception $e): string
    {
        return json_decode($e->getMessage()) ?: mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8');
    }

}
