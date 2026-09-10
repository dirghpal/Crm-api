<?php

use App\Exception\ApiStatusZeroException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


if (!function_exists('handleApiRequest')) {
    function handleApiRequest(Closure $callback)
    {

        try {
            $response = $callback();
            return $response;
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => 'formatErrors'($e->errors())
            ]);
        } catch (ApiStatusZeroException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            Log::error("Unhandled Exception: " . $e);
            return response()->json([
                'success' => false,
                'error' => 'Server error occurred!' . $e->getMessage()
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => formatErrors($e->errors())
            ], 200);
        }
    }

    function formatErrors($errors = [])
    {
        $error_array = [];

        if (count($errors) > 0) {
            foreach ($errors as $k => $v) {
                $error_array[$k] = $v[0];
            }
        }

        return $error_array;
    }
}
