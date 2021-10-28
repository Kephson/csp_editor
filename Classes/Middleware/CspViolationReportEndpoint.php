<?php

namespace RENOLIT\CspEditor\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

class CspViolationReportEndpoint implements MiddlewareInterface {

    /**
     * PSR-15 Middleware
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        /**
         * Get query paramameters and check for param "csp" which is the sign to use this middleware
         * example: https://my-url.de/?csp=violation-report-endpoint
         */
        $queryParams = $request->getQueryParams();
        if(!isset($queryParams['csp'])){
            return $handler->handle($request);
        }

        if ($json_data = json_decode($request->getBody())) {
            $json_data = json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            $this->writeLog($json_data);

            return $this->generateResponse();
        }

        return $this->generateResponse(400);

    }

    /**
     * @param int $status
     * @return Response
     */
    protected function generateResponse(int $status = 204): Response
    {
        /** @var  \TYPO3\CMS\Core\Http\Response $response */
        $response = new Response();
        $response = $response->withHeader('X-Rick-says','Never-Gonna-Give-You-Up');
        return $response->withStatus($status);
    }

    /**
     * @param $text
     */
    protected function writeLog($text): void
    {
        $file = $this->getLogFilePath();
        $stream = fopen($file, 'a+');
        fwrite($stream, $text . "\r\n");
        fclose($stream);
    }

    /**
     * @return string
     */
    protected function getLogFilePath(): string
    {
        $writer = new FileWriter(['logFileInfix' => 'csp_violation']);
        return $writer->getLogFile();
    }

}
