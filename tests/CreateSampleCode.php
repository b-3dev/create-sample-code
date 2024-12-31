<?php

/**
 * Class CreateSampleCode
 *
 * A class for generating cURL PHP code snippets dynamically based on given inputs like URL, method,
 * payload type, payload content, headers, and timeout settings.
 */

class CreateSampleCode
{
    private $inputUrl;
    private $inputMethod;
    private $inputPayloadType;
    private $inputPayload;
    private $inputHeaders = [];
    private $inputTimeout = 0;
    private $inputConnectTimeout = 0;
    private const ALLOW_METHODS = [
        'GET' => ['NONE', 'URL-ENCODE'],
        'POST' => ['NONE', 'URL-ENCODE', 'JSON', 'XML', 'MULTIPART', 'TEXT', 'BINARY', 'CUSTOM', 'GRAPHQL', 'YAML', 'HTML'],
        'PUT' => ['NONE', 'URL-ENCODE', 'JSON', 'XML', 'TEXT', 'BINARY', 'CUSTOM', 'GRAPHQL', 'YAML', 'HTML'],
        'DELETE' => ['NONE', 'URL-ENCODE', 'JSON', 'XML', 'TEXT', 'BINARY', 'CUSTOM', 'GRAPHQL', 'YAML', 'HTML'],
        'HEAD' => ['NONE'],
        'OPTIONS' => ['NONE', 'XML', 'JSON'],
        'PATCH' => ['NONE', 'URL-ENCODE', 'JSON', 'YAML'],
        'CUSTOM' => ['OPTIONAL']
    ];

    /**
     * Constructor to initialize the CreateSampleCode class.
     *
     * @param string $url    The URL for the HTTP request.
     * @param string $method The HTTP method (default is 'GET').
     * @throws InvalidArgumentException If the URL is invalid or method is unsupported.
     */
    public function __construct(string $url, string $method = 'GET')
    {
        $method = strtoupper($method);
        if (array_key_exists($method, self::ALLOW_METHODS)) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $this->inputUrl = $url;
                $this->inputMethod = $method;
            } else {
                throw new InvalidArgumentException("Invalid URL provided");
            }
        } else {
            throw new InvalidArgumentException("Invalid Method provided");
        }
    }

    /**
     * Set the payload type and content for the HTTP request.
     *
     * @param string $type    The payload type (e.g., 'JSON', 'URL-ENCODE').
     * @param mixed  $payload The payload content (e.g., array, JSON string).
     * @throws InvalidArgumentException If the payload type is unsupported for the given method.
     */
    public function setPayloads(string $type, $payload): void
    {
        $type = strtoupper($type);
        if (in_array($type, self::ALLOW_METHODS[$this->inputMethod])) {
            $this->inputPayloadType = $type;
            switch ($type) {
                case 'URL-ENCODE':
                    $this->inputPayload = http_build_query($payload);
                    break;
                case 'JSON':
                    $json = json_encode($payload);
                    $this->inputPayload = str_replace("\"", "\\\"", $json);
                    break;
                case 'NONE':
                    $this->inputPayload = [];
                    break;
                default:
                    $this->inputPayload = $payload;
            }
        } else {
            throw new InvalidArgumentException("it is not possible to use {$type} payload in {$this->inputMethod} method");
        }
    }

    /**
     * Set the headers for the HTTP request.
     *
     * @param array $headers Associative array of headers (e.g., ["Header-Name" => "value"]).
     * @throws InvalidArgumentException If headers are not provided as an associative array.
     */
    public function setHeaders(array $headers): void
    {
        if (!is_array($headers) || array_values($headers) === $headers) {
            throw new InvalidArgumentException("Headers must be an associative array of strings");
        }
        
        $this->inputHeaders = $headers;
    }

    /**
     * Configure timeout settings for the HTTP request.
     *
     * @param int $timeout         The request timeout duration in seconds (default is 30).
     * @param int $ConnectTimeout  The connection timeout duration in seconds (default is 10).
     */
    public function setTimeout(int $timeout = 30, int $ConnectTimeout = 10): void {
        $this->inputTimeout = $timeout;
        $this->inputConnectTimeout = $ConnectTimeout;
    }

    /**
     * Generate and return the PHP cURL code snippet based on the given inputs.
     *
     * @param int $webView Output format:
     *                     0 - Plain PHP code,
     *                     1 - HTML formatted code,
     *                     2 - Syntax-highlighted code.
     * @return string The generated PHP cURL code snippet.
     */
    public function fetchCode(int $webView = 0): string 
    {
        $curlCode  = "<?php\n" . PHP_EOL;
        if (!empty($this->inputPayload) && $this->inputMethod != 'GET') {
            $curlCode .= "\$payloads = \"{$this->inputPayload}\";\n\n";
        }

        if (!empty($this->inputHeaders)) {
            $headers = '';
            foreach ($this->inputHeaders as $key => $value) {
                $key = str_replace("\"", "\\\"", $key);
                $value = str_replace("\"", "\\\"", $value);
                $headers .= "\"$key: $value\",\n    ";
            }

            $curlCode .= "\$headers = [\n    {$headers}];\n\n";
        }
        
        $curlCode .= "\$ch = curl_init();\n";

        if ($this->inputMethod == 'GET') {
            $curlCode .= "curl_setopt(\$ch, CURLOPT_URL, \"{$this->inputUrl}?{$this->inputPayload}\");\n";
        } else {
            $curlCode .= "curl_setopt(\$ch, CURLOPT_URL, \"{$this->inputUrl}\");\n";
        }

        switch ($this->inputMethod) {
            case "GET":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_HTTPGET, true);\n";
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "POST":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_POST, true);\n";
                $curlCode .= (!empty($this->inputPayload)) ? "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$payloads);\n" : null;
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "PUT":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \"PUT\");\n";
                $curlCode .= (!empty($this->inputPayload)) ? "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$payloads);\n" : null;
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "DELETE":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \"DELETE\");\n";
                $curlCode .= (!empty($this->inputPayload)) ? "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$payloads);\n" : null;
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "PATCH":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \"PATCH\");\n";
                $curlCode .= (!empty($this->inputPayload)) ? "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$payloads);\n" : null;
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "OPTIONS":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \"OPTIONS\");\n";
                $curlCode .= (!empty($this->inputPayload)) ? "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$payloads);\n" : null;
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "HEAD":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_NOBODY, true);\n";
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "TRACE":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \"TRACE\");\n";
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
                break;
            case "CUSTOM":
                $curlCode .= "curl_setopt(\$ch, CURLOPT_CUSTOMREQUEST, \"{$this->inputMethod}\");\n";
                $curlCode .= (!empty($this->inputPayload)) ? "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \$payloads);\n" : null;
                $curlCode .= (!empty($this->inputHeaders)) ? "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n" : null;
        }
        
        $curlCode .= "curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);\n";
        $curlCode .= ($this->inputTimeout > 0) ? "curl_setopt(\$ch, CURLOPT_TIMEOUT, {$this->inputTimeout});\n" : null;
        $curlCode .= ($this->inputConnectTimeout > 0) ? "curl_setopt(\$ch, CURLOPT_CONNECTTIMEOUT, {$this->inputConnectTimeout});\n" : null;
        $curlCode .= "\$response = curl_exec(\$ch);\n\n";
        $curlCode .= "if(curl_errno(\$ch)) {\n";
        $curlCode .= "    die('cURL Error:' . curl_error(\$ch));\n";
        $curlCode .= "}\n\n";
        $curlCode .= "curl_close(\$ch);\n\n";
        $curlCode .= "var_dump(\$response);";

        $result = match ($webView) {
            0 => $curlCode,
            1 => '<pre>' . htmlspecialchars($curlCode) . '</pre>',
            2 => highlight_string($curlCode)
        };

        return $result;
    }
}

?>