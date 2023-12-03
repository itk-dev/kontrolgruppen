<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Tests;

use Kontrolgruppen\CoreBundle\Service\DatafordelerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Class DatafordelerServiceTest.
 */
final class DatafordelerServiceTest extends TestCase
{
    private static DatafordelerService $datafordelerService;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $mockResponseCPR = [
            new MockResponse(file_get_contents(__DIR__.'/testdata/cpr-test.json'), [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
            ]),
            new MockResponse(file_get_contents(__DIR__.'/testdata/cpr-test-bopaelssamling.json'), [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
            ]),
        ];
        $mockHttpClientCPR = new MockHttpClient($mockResponseCPR);

        $mockResponseCVR = [
            new MockResponse('[]', [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
            ]),
            new MockResponse('[]', [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
            ]),
        ];
        $mockHttpClientCVR = new MockHttpClient($mockResponseCVR);

        $mockResponseBBR = [
            new MockResponse('[]', [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
            ]),
            new MockResponse('[]', [
                'http_code' => 200,
                'response_headers' => ['Content-Type: application/json'],
            ]),
        ];
        $mockHttpClientBBR = new MockHttpClient($mockResponseBBR);
        self::$datafordelerService = new DatafordelerService($mockHttpClientCPR, $mockHttpClientCVR, $mockHttpClientBBR);
    }

    /**
     * @depends testGetStamdata
     */
    public function testGetStamdata(): void
    {
        $data = self::$datafordelerService->getPersonData('0712614382');
        self::assertTrue(isset($data['stamdata']));
        self::assertSame([
            'navn' => 'Karin Henriksen',
            'cpr' => '0712614382',
            'adresse' => 'Industrivænget 002 st th',
            'by' => '9000 Aalborg',
            'koen' => 'kvinde',
        ], $data['stamdata']);
    }

    /**
     * @depends testGetStamdata
     */
    public function testGetBopaelssamling(): void
    {
        $data = self::$datafordelerService->getPersonData('0712614382');
        self::assertTrue(isset($data['Bopaelssamling']));
        self::assertSame(json_decode(file_get_contents(__DIR__.'/assertdata/bopaelssamling.json'), true), $data['Bopaelssamling']);
    }
}
