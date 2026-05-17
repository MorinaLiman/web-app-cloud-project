<?php

function librarystore_build_headers(string $from, array $extraHeaders = []): array
{
    return array_merge([
        'From: LibraryStore <' . $from . '>',
        'Reply-To: ' . $from,
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion(),
    ], $extraHeaders);
}

function librarystore_smtp_send(string $to, string $subject, string $body, array $headers): bool
{
    $host = getenv('LIBRARYSTORE_SMTP_HOST') ?: 'localhost';
    $port = (int)(getenv('LIBRARYSTORE_SMTP_PORT') ?: 25);
    $timeout = 10;

    $stream = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$stream) {
        return false;
    }

    stream_set_timeout($stream, $timeout);

    $readLine = static function ($stream): string {
        $response = '';
        while (!feof($stream)) {
            $line = fgets($stream, 515);
            if ($line === false) {
                break;
            }
            $response .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    };

    $writeLine = static function ($stream, string $command): void {
        fwrite($stream, $command . "\r\n");
    };

    $expectOk = static function (string $response, array $codes): bool {
        foreach ($codes as $code) {
            if (strncmp($response, (string)$code, 3) === 0) {
                return true;
            }
        }
        return false;
    };

    $from = getenv('LIBRARYSTORE_MAIL_FROM') ?: 'info@librarystore.ks';
    $messageHeaders = implode("\r\n", $headers);
    $message = 'Subject: ' . $subject . "\r\n" . $messageHeaders . "\r\n\r\n" . $body;

    $response = $readLine($stream);
    if (!$expectOk($response, [220])) {
        fclose($stream);
        return false;
    }

    $writeLine($stream, 'EHLO localhost');
    $response = $readLine($stream);
    if (!$expectOk($response, [250])) {
        $writeLine($stream, 'HELO localhost');
        $response = $readLine($stream);
        if (!$expectOk($response, [250])) {
            fclose($stream);
            return false;
        }
    }

    $writeLine($stream, 'MAIL FROM:<' . $from . '>');
    if (!$expectOk($readLine($stream), [250])) {
        fclose($stream);
        return false;
    }

    $writeLine($stream, 'RCPT TO:<' . $to . '>');
    if (!$expectOk($readLine($stream), [250, 251])) {
        fclose($stream);
        return false;
    }

    $writeLine($stream, 'DATA');
    if (!$expectOk($readLine($stream), [354])) {
        fclose($stream);
        return false;
    }

    $message = preg_replace('/^\./m', '..', $message);
    $writeLine($stream, $message . "\r\n.");
    if (!$expectOk($readLine($stream), [250])) {
        fclose($stream);
        return false;
    }

    $writeLine($stream, 'QUIT');
    fclose($stream);

    return true;
}

function librarystore_mail(string $to, string $subject, string $body, array $extraHeaders = []): bool
{
    $from = getenv('LIBRARYSTORE_MAIL_FROM') ?: 'info@librarystore.ks';
    $headers = librarystore_build_headers($from, $extraHeaders);

    $smtpHost = getenv('LIBRARYSTORE_SMTP_HOST') ?: '';
    if ($smtpHost !== '' && librarystore_smtp_send($to, $subject, $body, $headers)) {
        return true;
    }

    if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
        if (function_exists('ini_set')) {
            @ini_set('SMTP', getenv('LIBRARYSTORE_SMTP_HOST') ?: 'localhost');
            @ini_set('smtp_port', getenv('LIBRARYSTORE_SMTP_PORT') ?: '25');
            @ini_set('sendmail_from', $from);
        }
    }

    return mail($to, $subject, $body, implode("\r\n", $headers));
}