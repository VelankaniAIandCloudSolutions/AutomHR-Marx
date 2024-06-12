<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Verify\V2;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\ListResource;
use Twilio\Options;
use Twilio\Rest\Verify\V2\Service\AccessTokenList;
use Twilio\Rest\Verify\V2\Service\EntityList;
use Twilio\Rest\Verify\V2\Service\MessagingConfigurationList;
use Twilio\Rest\Verify\V2\Service\RateLimitList;
use Twilio\Rest\Verify\V2\Service\VerificationCheckList;
use Twilio\Rest\Verify\V2\Service\VerificationList;
use Twilio\Rest\Verify\V2\Service\WebhookList;
use Twilio\Serialize;
use Twilio\Values;
use Twilio\Version;

/**
 * @property VerificationList $verifications
 * @property VerificationCheckList $verificationChecks
 * @property RateLimitList $rateLimits
 * @property MessagingConfigurationList $messagingConfigurations
 * @property EntityList $entities
 * @property WebhookList $webhooks
 * @property AccessTokenList $accessTokens
 * @method \Twilio\Rest\Verify\V2\Service\VerificationContext verifications(string $sid)
 * @method \Twilio\Rest\Verify\V2\Service\RateLimitContext rateLimits(string $sid)
 * @method \Twilio\Rest\Verify\V2\Service\MessagingConfigurationContext messagingConfigurations(string $country)
 * @method \Twilio\Rest\Verify\V2\Service\EntityContext entities(string $identity)
 * @method \Twilio\Rest\Verify\V2\Service\WebhookContext webhooks(string $sid)
 * @method \Twilio\Rest\Verify\V2\Service\AccessTokenContext accessTokens(string $sid)
 */
class ServiceContext extends InstanceContext {
    protected $_verifications;
    protected $_verificationChecks;
    protected $_rateLimits;
    protected $_messagingConfigurations;
    protected $_entities;
    protected $_webhooks;
    protected $_accessTokens;

    /**
     * Initialize the ServiceContext
     *
     * @param Version $version Version that contains the resource
     * @param string $sid The unique string that identifies the resource
     */
    public function __construct(Version $version, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['sid' => $sid, ];

        $this->uri = '/Services/' . \rawurlencode($sid) . '';
    }

    /**
     * Fetch the ServiceInstance
     *
     * @return ServiceInstance Fetched ServiceInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): ServiceInstance {
        $payload = $this->version->fetch('GET', $this->uri);

        return new ServiceInstance($this->version, $payload, $this->solution['sid']);
    }

    /**
     * Delete the ServiceInstance
     *
     * @return bool True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete(): bool {
        return $this->version->delete('DELETE', $this->uri);
    }

    /**
     * Update the ServiceInstance
     *
     * @param array|Options $options Optional Arguments
     * @return ServiceInstance Updated ServiceInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update(array $options = []): ServiceInstance {
        $options = new Values($options);

        $data = Values::of([
            'FriendlyName' => $options['friendlyName'],
            'CodeLength' => $options['codeLength'],
            'LookupEnabled' => Serialize::booleanToString($options['lookupEnabled']),
            'SkipSmsToLandlines' => Serialize::booleanToString($options['skipSmsToLandlines']),
            'DtmfInputRequired' => Serialize::booleanToString($options['dtmfInputRequired']),
            'TtsName' => $options['ttsName'],
            'Psd2Enabled' => Serialize::booleanToString($options['psd2Enabled']),
            'DoNotShareWarningEnabled' => Serialize::booleanToString($options['doNotShareWarningEnabled']),
            'CustomCodeEnabled' => Serialize::booleanToString($options['customCodeEnabled']),
            'Push.IncludeDate' => Serialize::booleanToString($options['pushIncludeDate']),
            'Push.ApnCredentialSid' => $options['pushApnCredentialSid'],
            'Push.FcmCredentialSid' => $options['pushFcmCredentialSid'],
            'Totp.Issuer' => $options['totpIssuer'],
            'Totp.TimeStep' => $options['totpTimeStep'],
            'Totp.CodeLength' => $options['totpCodeLength'],
            'Totp.Skew' => $options['totpSkew'],
            'DefaultTemplateSid' => $options['defaultTemplateSid'],
        ]);

        $payload = $this->version->update('POST', $this->uri, [], $data);

        return new ServiceInstance($this->version, $payload, $this->solution['sid']);
    }

    /**
     * Access the verifications
     */
    protected function getVerifications(): VerificationList {
        if (!$this->_verifications) {
            $this->_verifications = new VerificationList($this->version, $this->solution['sid']);
        }

        return $this->_verifications;
    }

    /**
     * Access the verificationChecks
     */
    protected function getVerificationChecks(): VerificationCheckList {
        if (!$this->_verificationChecks) {
            $this->_verificationChecks = new VerificationCheckList($this->version, $this->solution['sid']);
        }

        return $this->_verificationChecks;
    }

    /**
     * Access the rateLimits
     */
    protected function getRateLimits(): RateLimitList {
        if (!$this->_rateLimits) {
            $this->_rateLimits = new RateLimitList($this->version, $this->solution['sid']);
        }

        return $this->_rateLimits;
    }

    /**
     * Access the messagingConfigurations
     */
    protected function getMessagingConfigurations(): MessagingConfigurationList {
        if (!$this->_messagingConfigurations) {
            $this->_messagingConfigurations = new MessagingConfigurationList(
                $this->version,
                $this->solution['sid']
            );
        }

        return $this->_messagingConfigurations;
    }

    /**
     * Access the entities
     */
    protected function getEntities(): EntityList {
        if (!$this->_entities) {
            $this->_entities = new EntityList($this->version, $this->solution['sid']);
        }

        return $this->_entities;
    }

    /**
     * Access the webhooks
     */
    protected function getWebhooks(): WebhookList {
        if (!$this->_webhooks) {
            $this->_webhooks = new WebhookList($this->version, $this->solution['sid']);
        }

        return $this->_webhooks;
    }

    /**
     * Access the accessTokens
     */
    protected function getAccessTokens(): AccessTokenList {
        if (!$this->_accessTokens) {
            $this->_accessTokens = new AccessTokenList($this->version, $this->solution['sid']);
        }

        return $this->_accessTokens;
    }

    /**
     * Magic getter to lazy load subresources
     *
     * @param string $name Subresource to return
     * @return ListResource The requested subresource
     * @throws TwilioException For unknown subresources
     */
    public function __get(string $name): ListResource {
        if (\property_exists($this, '_' . $name)) {
            $method = 'get' . \ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown subresource ' . $name);
    }

    /**
     * Magic caller to get resource contexts
     *
     * @param string $name Resource to return
     * @param array $arguments Context parameters
     * @return InstanceContext The requested resource context
     * @throws TwilioException For unknown resource
     */
    public function __call(string $name, array $arguments): InstanceContext {
        $property = $this->$name;
        if (\method_exists($property, 'getContext')) {
            return \call_user_func_array(array($property, 'getContext'), $arguments);
        }

        throw new TwilioException('Resource does not have a context');
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Verify.V2.ServiceContext ' . \implode(' ', $context) . ']';
    }
}