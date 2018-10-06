<?php

namespace hydra\net\http;


interface HttpConstants {
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Carriage Return Line Feed */
    const CRLF = "\r\n";
    
    /** HTTP Version 1.0 */
    const HTTP_1_0 = "HTTP/1.0";
    
    /** HTTP Version 1.1 */
    const HTTP_1_1 = "HTTP/1.1";
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Plain Encoding */
    const ENC_TEXT_PLAIN = "text/plain";
    
    /** HTTP HTML Encoding */
    const ENC_TEXT_HTML = "text/html";
    
    /** HTTP Multipart Encoding */
    const ENC_MULTI = "multipart/form-data";
    
    /** HTTP JSON Encoding */
    const ENC_JSON = "application/json";
    
    const ENC_TEXT_JSON = "text/json";
    
    /** HTTP URL Encoding */
    const ENC_URL = "application/x-www-form-urlencoded";
    
    /** HTTP Custom Base64 URL Encoding */
    const ENC_B64URL = "application/b64url"; //< custom encoding
    
    const ENC_XML = "application/xml";
    
    const ENC_TEXT_XML = "text/xml";
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Transfer Encoding - chunked */
    const ENC_TRN_CHUNKED = "chunked";
    
    /** HTTP Transfer Encoding - compress */
    const ENC_TRN_COMPRESS = "compress";
    
    /** HTTP Transfer Encoding - deflate */
    const ENC_TRN_DEFLATE = "deflate";
    
    /** HTTP Transfer Encoding - gzip */
    const ENC_TRN_GZIP = "gzip";
    
    /** HTTP Transfer Encoding - identity */
    const ENC_TRN_IDENTITY = "identity";
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Timeout 30 Seconds */
    const TIMEOUT_SHORT     = 30;
    
    /** HTTP Timeout 1 Minute */
    const TIMEOUT_DEFAULT   = 60;
    
    /** HTTP Timeout 5 Minutes */
    const TIMEOUT_LONG      = 300;
    
    /** HTTP Default Port - 80 */
    const PORT_HTTP         = 80;
    
    /** HTTPS Default Port - 443 */
    const PORT_HTTPS        = 443;
    
    /** Localhost */
    const LOCALHOST         = "localhost";
    
    /* Default Buffer Size - 4096 bytes */
    const BUFFER_DEFAULT    = 4096;
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** User Agent - Internet Explorer 9 */
    const UA_IE_9           = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)";
    
    /** User Agent - Edge 25 */
    const UA_EDGE_25        = "Mozilla/5.0 (Windows NT 5.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Safari/537.36 Edge/13.10586";
    
    /** User Agent - Safari 10 */
    const UA_SAFARI_10      = "Mozilla/5.0 (iPad; CPU OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Version/10.0 Mobile/14D27 Safari/602.1";
    
    /** User Agent - Chrome 64 */
    const UA_CHROME_64      = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36";
    
    /**
     * Common HTTP request methods
     */
    const METHOD_CONNECT    = "CONNECT";
    const METHOD_DELETE     = "DELETE";
    const METHOD_GET        = "GET";
    const METHOD_HEAD       = "HEAD";
    const METHOD_OPTIONS    = "OPTIONS";
    const METHOD_PATCH      = "PATCH";
    const METHOD_POST       = "POST";
    const METHOD_PUT        = "PUT";
    const METHOD_TRACE      = "TRACE";
    
    const MAX_REDIRECTS = 10;
    
    /**
     * HTTP Status Codes
     */
    const STATUS_CODE_CONTINUE = 100;
    const STATUS_CODE_SWITCHING_PROTOCOLS = 101;
    const STATUS_CODE_PROCESSING = 102;
    const STATUS_CODE_EARLY_HINTS = 103;
    const STATUS_CODE_OK = 200;
    const STATUS_CODE_CREATED = 201;
    const STATUS_CODE_ACCEPTED = 202;
    const STATUS_CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    const STATUS_CODE_NO_CONTENT = 204;
    const STATUS_CODE_RESET_CONTENT = 205;
    const STATUS_CODE_PARTIAL_CONTENT = 206;
    
    const STATUS_CODE_MULTIPLE_CHOICES = 300;
    const STATUS_CODE_MOVED_PERMANENTLY = 301;
    const STATUS_CODE_FOUND = 302;
    const STATUS_CODE_SEE_OTHER = 303;
    const STATUS_CODE_NOT_MODIFIED = 304;
    const STATUS_CODE_USE_PROXY = 305;
    const STATUS_CODE_TEMPORARY_REDIRECT = 307;
    
    const STATUS_CODE_BAD_REQUEST = 400;
    const STATUS_CODE_UNAUTHORIZED = 401;
    const STATUS_CODE_PAYMENT_REQUIRED = 402;
    const STATUS_CODE_FORBIDDEN = 403;
    const STATUS_CODE_NOT_FOUND = 404;
    const STATUS_CODE_METHOD_NOT_ALLOWED = 405;
    const STATUS_CODE_NOT_ACCEPTABLE = 406;
    const STATUS_CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    const STATUS_CODE_REQUEST_TIMEOUT = 408;
    const STATUS_CODE_CONFLICT = 409;
    const STATUS_CODE_GONE = 410;
    const STATUS_CODE_LENGTH_REQUIRED = 411;
    const STATUS_CODE_PRECONDITION_FAILED = 412;
    const STATUS_CODE_REQUEST_ENTITY_TOO_LARGE = 413;
    const STATUS_CODE_REQUEST_URI_TOO_LARGE = 414;
    const STATUS_CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const STATUS_CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const STATUS_CODE_EXPECTATION_FAILED = 417;
    const STATUS_CODE_IM_A_TEAPOT = 418;
    const STATUS_CODE_MISDIRECTED_REQUESTED = 421;
    const STATUS_CODE_UNPROCESSABLE_ENTITY = 422;
    const STATUS_CODE_LOCKED = 423;
    const STATUS_CODE_FAILED_DEPENDENCY = 424;
    const STATUS_CODE_UPGRADE_REQUIRED = 426;
    const STATUS_CODE_PRECONDITION_REQUIRED = 428;
    const STATUS_CODE_TOO_MANY_REQUESTS = 429;
    const STATUS_CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const STATUS_CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    
    const STATUS_CODE_INTERNAL_SERVER_ERROR = 500;
    const STATUS_CODE_NOT_IMPLEMENTED = 501;
    const STATUS_CODE_BAD_GATEWAY = 502;
    const STATUS_CODE_SERVICE_UNAVAILABLE = 503;
    const STATUS_CODE_GATEWAY_TIMEOUT = 504;
    const STATUS_CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
    const STATUS_CODE_VARIANT_ALSO_NEGOTIATES = 506;
    const STATUS_CODE_INSUFFICIENT_STORAGE = 507;
    const STATUS_CODE_LOOP_DETECTED = 508;
    const STATUS_CODE_NOT_EXTENDED = 510;
    const STATUS_CODE_NETWORK_AUTHENTICATION_REQUIRED = 511;
    
}