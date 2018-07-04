<?php

namespace hydra\net;


interface HttpConstants {
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Carriage Return Line Feed */
    const CRLF              = "\r\n";
    
    /** HTTP Version 1.0 */
    const HTTP_1_0           = "HTTP/1.0";
    
    /** HTTP Version 1.1 */
    const HTTP_1_1           = "HTTP/1.1";
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Plain Encoding */
    const ENC_PLAIN         = "text/plain";
    
    /** HTTP Multipart Encoding */
    const ENC_MULTI         = "multipart/form-data";
    
    /** HTTP JSON Encoding */
    const ENC_JSON          = "application/json";
    
    /** HTTP URL Encoding */
    const ENC_URL           = "application/x-www-form-urlencoded";
    
    /** HTTP Custom Base64 URL Encoding */
    const ENC_B64URL        = "application/b64url"; //< custom encoding
    
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= //
    
    /** HTTP Transfer Encoding - chunked */
    const ENC_TRN_CHUNKED   = "chunked";
    
    /** HTTP Transfer Encoding - compress */
    const ENC_TRN_COMPRESS  = "compress";
    
    /** HTTP Transfer Encoding - deflate */
    const ENC_TRN_DEFLATE   = "deflate";
    
    /** HTTP Transfer Encoding - gzip */
    const ENC_TRN_GZIP      = "gzip";
    
    /** HTTP Transfer Encoding - identity */
    const ENC_TRN_IDENTITY  = "identity";
    
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
    
    
    const STATUS_CODE_OK    = 200;
    const STATUS_TEXT_OK    = "OK";
    
}