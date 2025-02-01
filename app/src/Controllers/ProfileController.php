<?php

namespace App\Controllers;

use App\Services\ProfileService;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Psr7\Utils;
use App\Loggers\ProfileLogger;

class ProfileController extends AbstractController
{
    protected ProfileService $profileService;

    protected ProfileLogger $logger;

    public function __construct()
    {
        parent::__construct();
        $this->profileService = new ProfileService();

        $this->logger = new ProfileLogger(__DIR__ . '/../logs/profile.log');
    }

    public function createProfile(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!isset(
            $data['user_id'],
            $data['phone'],
            $data['actual_address'],
            $data['legal_address'],
            $data['company_name'],
            $data['inn']
        )) {
            return $response->withStatus(400)->withBody(Utils::streamFor('Invalid input'));
        }

        try {
            $profileId = $this->profileService->createProfile($data);

            return $this->prepareJsonResponse($response, ['profile_id' => $profileId]);
        } catch (Exception $e) {
            $this->logger->logError('Profile created failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function getProfiles(Request $request, Response $response): Response
    {
        try {
            $profiles = $this->profileService->getProfiles();

            if (count($profiles) > 0) {
                $response->getBody()->write(json_encode($profiles));
                return $response->withHeader('Content-Type', 'application/json');
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('Not Found'));
        } catch (Exception $e) {
            $this->logger->logError('Get profiles failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function getProfileByUserId(Request $request, Response $response, array $args): Response
    {
        try {
            $profile = $this->profileService->getProfileByUserId($args['userId']);
            $this->logger->logInfo('About logger', ['logger' => $this->logger]);

            if ($profile) {
                $response->getBody()->write(json_encode($profile));
                return $response->withHeader('Content-Type', 'application/json');
            }

            return $response->withStatus(404)->withBody(Utils::streamFor('Not Found'));
        } catch (Exception $e) {
            $this->logger->logError('Get profile failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }

    public function updateProfile($id, $request): void
    {
        $data = $request->getParsedBody();
        $this->profileService->updateProfile($id, $data);
    }

    public function deleteProfile(Request $request, Response $response, array $args): Response
    {
        try {
            $profile = $this->profileService->getProfileById($args['id']);
            if ($profile === null) {
                return $response->withStatus(404)->withBody(Utils::streamFor('Profile Not Found'));
            }

            if ($this->profileService->deleteProfile($args['id'])) {
                return $response->withStatus(200)->withBody(Utils::streamFor('Profile deleted'));
            }
        } catch (Exception $e) {
            $this->logger->logError('Delete profile failed', ['error' => $e->getMessage()]);
            return $response->withStatus(500)->withBody(Utils::streamFor('Internal Server Error'));
        }
    }
}
