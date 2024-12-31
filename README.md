# CreateSampleCode - PHP cURL Code Generator

This PHP class provides a convenient way to generate cURL code for making HTTP requests with customizable headers, payloads, and request methods. It's particularly useful for generating cURL code snippets to interact with APIs or other web services in PHP.

## Features
- **Supports multiple HTTP methods**: `GET`, `POST`, `PUT`, `DELETE`, `HEAD`, `PATCH`, `OPTIONS`, `CUSTOM`.
- **Flexible Payloads**: Supports various payload types such as `NONE`, `URL-ENCODE`, `JSON`, `XML`, `TEXT`, `BINARY`, `GRAPHQL`, `YAML`, `HTML`.
- **Custom Headers**: Allows custom HTTP headers to be set for the request.
- **Timeout Settings**: Configurable timeouts for both connection and response.
- **cURL Code Generation**: Generates PHP cURL code that can be directly used to send HTTP requests.

## Installation

### Using Composer (Recommended)

1. Install Composer if you haven't already. You can download and install Composer by following the instructions on the [official Composer website](https://getcomposer.org/download/).

2. Add this package to your project via Composer by running the following command in your project directory:
   
```bash
composer require b-3dev/create-sample-code
```

### Manual Installation

If you prefer not to use Composer, you can clone or download this repository to your project:

```bash
git clone https://github.com/b-3dev/create-sample-code.git
```

## Usage

### 1. Create an instance of the `CreateSampleCode` class:

```php
$code = new CreateSampleCode('https://req.wiki-api.ir/apis-1/ChatGPT', 'GET');
```

### 2. Set the payload (if applicable):

```php
$code->setPayloads('url-encode', [
    "q" => "hello AI",
]);
```

### 3. Set custom headers (if applicable):

```php
$code->setHeaders([
    "Content-Type" => "application/json"
]);
```

### 4. Set the timeout (optional):

```php
$code->setTimeout(30, 10);  // Timeout: 30 seconds, Connect Timeout: 10 seconds
```

### 5. Fetch the generated cURL code:

```php
echo $code->fetchCode(2);  // Option 2 shows the code with syntax highlighting
```

### Supported Methods and Payloads

- **GET**: Supports `NONE`, `URL-ENCODE`.
- **POST**: Supports `NONE`, `URL-ENCODE`, `JSON`, `XML`, `MULTIPART`, `TEXT`, `BINARY`, `CUSTOM`, `GRAPHQL`, `YAML`, `HTML`.
- **PUT**: Supports `NONE`, `URL-ENCODE`, `JSON`, `XML`, `TEXT`, `BINARY`, `CUSTOM`, `GRAPHQL`, `YAML`, `HTML`.
- **DELETE**: Supports `NONE`, `URL-ENCODE`, `JSON`, `XML`, `TEXT`, `BINARY`, `CUSTOM`, `GRAPHQL`, `YAML`, `HTML`.
- **PATCH**: Supports `NONE`, `URL-ENCODE`, `JSON`, `YAML`.
- **OPTIONS**: Supports `NONE`, `XML`, `JSON`.
- **CUSTOM**: Supports custom methods.

## Example

Here’s an example of how you can use the `CreateSampleCode` class to generate cURL code for a GET request with URL-Encode payload:

```php
$code = new CreateSampleCode('https://req.wiki-api.ir/apis-1/ChatGPT', 'GET');
$code->setPayloads('url-encode', [
    "q" => "hello AI"
]);
$code->setHeaders([
    "Content-Type" => "application/json"
]);
$code->setTimeout(30, 10);
echo $code->fetchCode(2);
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Need Help?

If you encounter any issues or have questions about this project, feel free to open an issue in the [GitHub repository](https://github.com/b-3dev/create-sample-code/issues). I'll be happy to assist you!