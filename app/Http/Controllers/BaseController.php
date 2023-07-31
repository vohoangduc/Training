<?php

namespace App\Http\Controllers;

use App\Constants\Constant;
use App\Http\Controllers\Controller;
use App\Models\PushNotification;
use App\Notifications\PushFirebaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="OYAMA",
 *      description="API document",
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )

 * *  * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="bearerAuth",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearer",
 * )
 */
class BaseController extends Controller
{
    /**
     * log to file.
     *
     */
    public function log($file = null, $funct = null, $data = null, $message = null)
    {
        $log = 'ERROR';
        if (!is_null($file)) {
            $log = $log . PHP_EOL . '- FILE: ' . $file;
        }
        if (!is_null($funct)) {
            $log = $log . PHP_EOL . '- FUNCTION: ' . $funct;
        }
        if (!is_null($data)) {
            $log = $log . PHP_EOL . '- DATA: ' . json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        if (!is_null($message)) {
            if (is_array($message)) {
                $message = json_encode($message);
            }
            $log = $log . PHP_EOL . '- MESSAGE: ' . $message . PHP_EOL;
        }
        Log::channel('log_error')->info($log);
    }

    /**
     * return success response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendSuccessResponse($message = null, $data = null, $code = null)
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        if ($code) {
            return response()->json($response, $code);
        }

        return response()->json($response);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($message = null, $code = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($code) {
            return response()->json($response, $code);
        } else {
            return response()->json($response);
        }
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendEmail($page, $toEmail, $bccEmail, $data, $title)
    {
        try {
            $email = config('mail.from')['address'];
            $name_email = config('mail.from')['name'];

            Mail::send(['text' => $page], $data, function ($message) use ($toEmail, $bccEmail, $email, $name_email, $title) {
                $message->from($email, $name_email)->subject($title);
                $message->to($toEmail);

                if (!empty($bccEmail)) {
                    $message->bcc($bccEmail);
                }

                $message->setContentType('text/plain');
            });

        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($path, $name, $headers)
    {
        try {
            $file = storage_path($path);

            if (file_exists($file)) {
                return response()->download($file, $name, $headers);
            } else {
                return $this->sendError(__('app.not_exist', ['attribute' => __('app.file')]), Response::HTTP_NOT_FOUND);
            }

        } catch (\Exception $e) {
            logger($e->getMessage());
        }
    }

    /**
     * Check client role
     *
     * @return view or boolean
     */
    public function clientPermission()
    {
        try {
            $user = Auth::user();

            if ($user->role != Constant::ROLE['EXECUTIVES']) {
                $this->log(self::class, __FUNCTION__, null, __('app.not_have_permission'));
                abort(404);
            }

        } catch (\Exception $e) {
            $this->log(self::class, __FUNCTION__, null, $e->getMessage());
            abort(500);
        }
    }

    /**
    * handle push notification
    *
    * @param $title,
    * @param $body
    * @param $user_ids
    * @return \Illuminate\Http\JsonResponse
    */
    public function sendNotification($title, $body, $userIds, $type)
    {
        $deviceTokens = PushNotification::whereNotNull('fcm_token')
                                        ->whereIn('user_id', $userIds)
                                        ->pluck('fcm_token')
                                        ->all();
        Notification::send(null, new PushFirebaseNotification($title, $body, $deviceTokens, $type));
    }

}
