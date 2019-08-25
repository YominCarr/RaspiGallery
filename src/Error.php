<?php

error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", __DIR__ . "/error.log");

const RESPONSE_NO_ERROR = 0;
const RESPONSE_ERROR_STATE_CHANGED = 1 << 0;
const RESPONSE_ERROR_UNKNOWN = 1 << 1;
const RESPONSE_ERROR_SQL_GENERIC = 1 << 2;
const RESPONSE_ERROR_UNKNOWN_TABLE = 1 << 3;
const RESPONSE_ERROR_DATABASE_IO_CONFLICT = 1 << 4;
const RESPONSE_ERROR_INCONSISTENT_STATE = 1 << 5;
const RESPONSE_ERROR_MISSING_INPUT = 1 << 6;


function encapsulateDataAndResponseInJson($responseType, $message, $data) {
    return json_encode(array('responseType' => $responseType, 'message' => $message, 'data' => $data));
}
