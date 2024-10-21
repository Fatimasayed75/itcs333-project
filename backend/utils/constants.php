<?php

namespace Utils;

class Constants
{
    // Database-related
    public const DB_CONNECTION_ERROR = 'Failed to connect to the database';

    // User-related
    public const USER_NOT_FOUND = 'User not found';

    // room-related
    public const ROOM_NOT_FOUND = 'Room not found';
    
    // Comment-related
    public const COMMENT_NOT_FOUND = 'Comment not found';

    // Records-related
    public const RECORD_NOT_FOUND = 'Record not found';
    public const NO_RECORDS = 'No records found';

    // reply-related
    public const REPLY_NOT_FOUND = "Reply not found";

    // null values
    public const NULL_VALUE_FOUND = "Null values are not accepted";


    // HTTP Status
    public const BAD_REQUEST_404 = 'Bad Request';
    public const NOT_FOUND_404 = 'Not Found';
    public const METHOD_NOT_ALLOWED_405 = 'Method Not Allowed';
    public const INTERNAL_SERVER_ERROR_500 = 'Internal Server Error';

}
