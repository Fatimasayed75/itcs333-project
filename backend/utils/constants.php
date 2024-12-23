<?php

namespace Utils;

class Constants
{
    // Database-related
    public const DB_CONNECTION_ERROR = 'Failed to connect to the database';
    public const DB_INSERTION_ERROR = 'Failed to insert to the database';

    // User-related
    public const USER_NOT_FOUND = 'User not found';

    // room-related
    public const ROOM_NOT_FOUND = 'Room not found';

    // Comment-related
    public const COMMENT_NOT_FOUND = 'Comment not found';

    // Booking-related
    public const BOOKING_NOT_FOUND = 'Booking not found';
    public const BOOKING_DURATION_TOO_SHORT = 'Booking duration must be at least 30 minutes';
    public const BOOKING_DURATION_TOO_LONG = 'Booking duration must be at most 2 hours and 30 minutes';
    public const BOOKING_CONFLICT = 'Booking conflicts with another booking';
    public const INVALID_END_TIME = 'End time must be later than start time and maximum 18:00';
    public const INVALID_START_TIME = 'Start time must be at least after an hour from now and maximum 17:30';
    public const START_TIME_IN_PAST = 'Start time must not be in the past';
    public const END_TIME_IN_PAST = 'End time must not be in the past';
    public const INVALID_BOOKING_DAY = 'Booking is not allowed on Friday';

    // Records-related
    public const RECORD_NOT_FOUND = 'Record not found';
    public const NO_RECORDS = 'No records found';

    // reply-related
    public const REPLY_NOT_FOUND = "Reply not found";

    // null values
    public const NULL_VALUE_FOUND = "Null values are not accepted";

    // Operations
    public const SUCCESS = 'Operation executed successfully';
    public const FAILED = 'Operation failed';

    // HTTP Status
    public const BAD_REQUEST_404 = 'Bad Request';
    public const NOT_FOUND_404 = 'Not Found';
    public const METHOD_NOT_ALLOWED_405 = 'Method Not Allowed';
    public const INTERNAL_SERVER_ERROR_500 = 'Internal Server Error';

    // Admin Email
    public const ADMIN_EMAIL = 'admin@uob.edu.bh';
    public const ADMIN_USER_ID = 62;

    public const GUEST_USER_ID = 0;

    // DEFAULT PICTURE ID
    public const DEFAULT_PICTURE_ID = 1;

}
