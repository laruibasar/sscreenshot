<?php

namespace App\Service;

use App\Entity\Screenshot;
use DateTime;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttperService
{
    private $client;

    private $endpoint;

    private $token;

    private $screenshotsDir;

    public function __construct(HttpClientInterface $client, string $apiEndpoint, string $token, string $screenshotsDir)
    {
        $this->client = $client;
        $this->endpoint = $apiEndpoint;
        $this->token = $token;
        $this->screenshotsDir = $screenshotsDir;
    }

    public function fetchScreenshot(Screenshot $screenshot, DateTime $timestamp): object
    {
        $filename = '';
        $params = $this->setParameters($screenshot);
        $params += ['token' => $this->token];
        $size = 0;

        $response = $this->client->request(
            'GET',
            $this->endpoint,
            ['query' => $params]
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $contentLength = $response->getHeaders()['content-length'][0];
        $content = $response->getContent();

        if ($screenshot->getOutput() === 'image') {
            $file = $this->write($screenshot, $content, $contentLength, $timestamp);
            if (!$file->success) {
                return $file;
            }
            $filename = $file->filename;
            $size = $file->size;
        } else {
            // We don't save any bytes but maybe the link to image?
            $json = json_decode($content);
            $filename = $json->screenshot;
        }


        return (object)[
            'success' => $statusCode == 200,
            'filename' => $filename,
            'size' => $size,
            'timestamp' => $timestamp
        ];
    }

    /**
     * Prepare the query parameters using the entity and a serializer.
     * Because not all parameters are the same, we need to remove the unused ones,
     * mostly to avoid problems with the GET request.
     *
     * @param Screenshot $screenshot
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function setParameters(Screenshot $screenshot): array
    {
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $params = $normalizer->normalize($screenshot);

        // Convert parameters to api definition for clipping,
        // we need to urlencode() to follow the key -> value:
        //  clip[x] = value
        // Note: only if set do we send this, could have side effects if
        //       values are null and cast to 0
        if (
            isset($params['clipx'])
            && isset($params['clipy'])
            && isset($params['clip_width'])
            && isset($params['clip_height'])
        ) {
            $params = $params
                + [urlencode('clip[x]') => $params['clipx']]
                + [urlencode('clip[y]') => $params['clipy']]
                + [urlencode('clip[width]') => $params['clip_width']]
                + [urlencode('clip[height]') => $params['clip_height']];
            var_dump($params); die();
        }

        // Remove to clear the wrong named parameters
        unset($params['clipx']);
        unset($params['clipy']);
        unset($params['clip_width']);
        unset($params['clip_height']);

        // Not parameters for the api
        unset($params['id']);
        unset($params['created_on']);
        unset($params['filename']);
        unset($params['success']);

        return $params;
    }

    /**
     * Write to file the image content receive from the api.
     *
     * @param Screenshot $screenshot
     * @param string $content
     * @param int $length
     * @param DateTime $time
     * @return object
     */
    private function write(Screenshot $screenshot, string $content, int $length, DateTime $time): object
    {
        $filesystem = new Filesystem();

        $url = str_replace(['%2F', '.', ':', '/', '\\', '?', ';', '=', '&'], '_', $screenshot->getUrl());
        $filename = $url
            . '_' . $time->format('YmdHisu')
            . '.' . $screenshot->getFileType();

        if (!$filesystem->exists($this->screenshotsDir)) {
            try {
                $filesystem->mkdir($this->screenshotsDir);
            } catch (IOExceptionInterface $exception) {
                return (object)[
                    'success' => false,
                    'errorMessage' => $exception->getMessage()
                ];
            }
        }

        if (!$file = fopen($this->screenshotsDir . $filename, 'w')) {
            return (object)[
                'success' => false,
                'errorMessage' => "Cannot open file $filename."
            ];
        }
        $size = fwrite($file, $content, $length);
        if ($size === false) {
            return (object)[
                'success' => false,
                'errorMessage' => "Cannot write to file $filename."
            ];
        }
        fclose($file);

        return (object)[
            'success' => true,
            'filename' => $this->screenshotsDir . $filename,
            'size' => $size
        ];
    }
}
