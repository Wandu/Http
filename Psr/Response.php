<?php
namespace Wandu\Http\Psr;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Wandu\Http\Traits\ResponseTrait;

class Response extends Message implements ResponseInterface
{
    use ResponseTrait;

    /* Http Status Code, http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml */
    const HTTP_STATUS_CONTINUE = 100;
    const HTTP_STATUS_SWITCHING_PROTOCOLS = 101;
    const HTTP_STATUS_PROCESSING = 102;

    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_CREATED = 201;
    const HTTP_STATUS_ACCEPTED = 202;
    const HTTP_STATUS_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_STATUS_NO_CONTENT = 204;
    const HTTP_STATUS_RESET_CONTENT = 205;
    const HTTP_STATUS_PARTIAL_CONTENT = 206;
    const HTTP_STATUS_MULTI_STATUS = 207;
    const HTTP_STATUS_ALREADY_REPORTED = 208;
    const HTTP_STATUS_IM_USED = 226;

    const HTTP_STATUS_MULTIPLE_CHOICES = 300;
    const HTTP_STATUS_MOVED_PERMANENTLY = 301;
    const HTTP_STATUS_FOUND = 302;
    const HTTP_STATUS_SEE_OTHER = 303;
    const HTTP_STATUS_NOT_MODIFIED = 304;
    const HTTP_STATUS_USE_PROXY = 305;
    const HTTP_STATUS_SWITCH_PROXY = 306; // Deprecated
    const HTTP_STATUS_TEMPORARY_REDIRECT = 307;
    const HTTP_STATUS_PERMANENT_REDIRECT = 308;

    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_UNAUTHORIZED = 401;
    const HTTP_STATUS_PAYMENT_REQUIRED = 402;
    const HTTP_STATUS_FORBIDDEN = 403;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_METHOD_NOT_ALLOWED = 405;
    const HTTP_STATUS_NOT_ACCEPTABLE = 406;
    const HTTP_STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_STATUS_REQUEST_TIMEOUT = 408;
    const HTTP_STATUS_CONFLICT = 409;
    const HTTP_STATUS_GONE = 410;
    const HTTP_STATUS_LENGTH_REQUIRED = 411;
    const HTTP_STATUS_PRECONDITION_FAILED = 412;
    const HTTP_STATUS_PAYLOAD_TOO_LARGE = 413;
    const HTTP_STATUS_URI_TOO_LONG = 414;
    const HTTP_STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_STATUS_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_STATUS_EXPECTATION_FAILED = 417;
    const HTTP_STATUS_I_AM_A_TEOPOT = 418;
    const HTTP_STATUS_MISDIRECTED_REQUEST = 421;
    const HTTP_STATUS_UNPROCESSABLE_ENTITY = 422;
    const HTTP_STATUS_LOCKED = 423;
    const HTTP_STATUS_FAILED_DEPENDENCY = 424;
    const HTTP_STATUS_UNORDERED_COLLECTION = 425;
    const HTTP_STATUS_UPGRADE_REQUIRED = 426;
    const HTTP_STATUS_PRECONDITION_REQUIRED = 428;
    const HTTP_STATUS_TOO_MANY_REQUESTS = 429;
    const HTTP_STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const HTTP_STATUS_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
    const HTTP_STATUS_NOT_IMPLEMENTED = 501;
    const HTTP_STATUS_BAD_GATEWAY = 502;
    const HTTP_STATUS_SERVICE_UNAVAILABLE = 503;
    const HTTP_STATUS_GATEWAY_TIMEOUT = 504;
    const HTTP_STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_STATUS_VARIANT_ALSO_NEGOTIATES = 506;
    const HTTP_STATUS_INSUFFICIENT_STORAGE = 507;
    const HTTP_STATUS_LOOP_DETECTED = 508;
    const HTTP_STATUS_NOT_EXTENDED = 510;
    const HTTP_STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;
    
    /**
     * @param int $statusCode
     * @param \Psr\Http\Message\StreamInterface $body
     * @param array $headers
     * @param string $reasonPhrase
     * @param string $protocolVersion
     */
    public function __construct(
        $statusCode = 200,
        StreamInterface $body = null,
        array $headers = [],
        $reasonPhrase = '',
        $protocolVersion = '1.1'
    ) {
        $this->validStatusCode($statusCode);

        $this->statusCode = $statusCode;
        $this->reasonPhrase = $this->filterReasonPhrase($statusCode, $reasonPhrase);

        parent::__construct($body, $headers, $protocolVersion);
    }
}
