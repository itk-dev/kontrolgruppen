<?php

/*
 * This file is part of itk-dev/kontrolgruppen.
 *
 * (c) 2019–2021 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\CPR;

use ItkDev\AzureKeyVault\Authorisation\VaultToken;
use ItkDev\AzureKeyVault\Exception\TokenException;
use ItkDev\AzureKeyVault\KeyVault\VaultSecret;
use ItkDev\Serviceplatformen\Certificate\AzureKeyVaultCertificateLocator;
use ItkDev\Serviceplatformen\Request\InvocationContextRequestGenerator;
use ItkDev\Serviceplatformen\Service\PersonBaseDataExtendedService;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * Class ServiceplatformenServiceFactory.
 */
class ServiceplatformenServiceFactory
{
    /**
     * Factory for CPR service.
     *
     * @param ClientInterface         $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param string                  $azureTenantId
     * @param string                  $azureApplicationId
     * @param string                  $azureClientSecret
     * @param string                  $azureKeyVaultName
     * @param string                  $azureKeyVaultSecret
     * @param string                  $azureKeyVaultSecretVersion
     * @param string                  $serviceplatformenServiceAgreementUuid
     * @param string                  $serviceplatformenUserSystemUuid
     * @param string                  $serviceplatformenUserUuid
     * @param string                  $serviceContractFilename
     * @param string                  $serviceEndpoint
     * @param string                  $serviceUuid
     *
     * @return PersonBaseDataExtendedService
     *
     * @throws CprException
     */
    public static function createPersonBaseDataExtendedService(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, string $azureTenantId, string $azureApplicationId, string $azureClientSecret, string $azureKeyVaultName, string $azureKeyVaultSecret, string $azureKeyVaultSecretVersion, string $serviceplatformenServiceAgreementUuid, string $serviceplatformenUserSystemUuid, string $serviceplatformenUserUuid, string $serviceContractFilename, string $serviceEndpoint, string $serviceUuid)
    {
        try {
            $vaultToken = new VaultToken($httpClient, $requestFactory);
            $token = $vaultToken->getToken(
                $azureTenantId,
                $azureApplicationId,
                $azureClientSecret
            );
        } catch (TokenException $e) {
            throw new CprException($e->getMessage(), $e->getCode());
        }

        $vault = new VaultSecret(
            $httpClient,
            $requestFactory,
            $azureKeyVaultName,
            $token->getAccessToken()
        );

        $certificateLocator = new AzureKeyVaultCertificateLocator(
            $vault,
            $azureKeyVaultSecret,
            $azureKeyVaultSecretVersion
        );

        $soapClientOptions = [
            'wsdl' => $serviceContractFilename,
            'certificate_locator' => $certificateLocator,
            'options' => [
                'location' => $serviceEndpoint,
            ],
        ];

        if (!realpath($serviceContractFilename)) {
            throw new CprException(sprintf('The path (%s) to the service contract is invalid.', $serviceContractFilename));
        }

        $requestGenerator = new InvocationContextRequestGenerator(
            $serviceplatformenServiceAgreementUuid,
            $serviceplatformenUserSystemUuid,
            $serviceUuid,
            $serviceplatformenUserUuid
        );

        return new PersonBaseDataExtendedService($soapClientOptions, $requestGenerator);
    }
}
