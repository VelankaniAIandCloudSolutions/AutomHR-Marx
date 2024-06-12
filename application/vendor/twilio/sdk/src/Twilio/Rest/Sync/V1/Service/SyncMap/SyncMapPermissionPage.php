<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Sync\V1\Service\SyncMap;

use Twilio\Http\Response;
use Twilio\Page;
use Twilio\Version;

class SyncMapPermissionPage extends Page {
    /**
     * @param Version $version Version that contains the resource
     * @param Response $response Response from the API
     * @param array $solution The context solution
     */
    public function __construct(Version $version, Response $response, array $solution) {
        parent::__construct($version, $response);

        // Path Solution
        $this->solution = $solution;
    }

    /**
     * @param array $payload Payload response from the API
     * @return SyncMapPermissionInstance \Twilio\Rest\Sync\V1\Service\SyncMap\SyncMapPermissionInstance
     */
    public function buildInstance(array $payload): SyncMapPermissionInstance {
        return new SyncMapPermissionInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['mapSid']
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        return '[Twilio.Sync.V1.SyncMapPermissionPage]';
    }
}