<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Chat\V1\Service;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\ListResource;
use Twilio\Options;
use Twilio\Rest\Chat\V1\Service\Channel\InviteList;
use Twilio\Rest\Chat\V1\Service\Channel\MemberList;
use Twilio\Rest\Chat\V1\Service\Channel\MessageList;
use Twilio\Values;
use Twilio\Version;

/**
 * @property MemberList $members
 * @property MessageList $messages
 * @property InviteList $invites
 * @method \Twilio\Rest\Chat\V1\Service\Channel\MemberContext members(string $sid)
 * @method \Twilio\Rest\Chat\V1\Service\Channel\MessageContext messages(string $sid)
 * @method \Twilio\Rest\Chat\V1\Service\Channel\InviteContext invites(string $sid)
 */
class ChannelContext extends InstanceContext {
    protected $_members;
    protected $_messages;
    protected $_invites;

    /**
     * Initialize the ChannelContext
     *
     * @param Version $version Version that contains the resource
     * @param string $serviceSid The SID of the Service to fetch the resource from
     * @param string $sid The unique string that identifies the resource
     */
    public function __construct(Version $version, $serviceSid, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['serviceSid' => $serviceSid, 'sid' => $sid, ];

        $this->uri = '/Services/' . \rawurlencode($serviceSid) . '/Channels/' . \rawurlencode($sid) . '';
    }

    /**
     * Fetch the ChannelInstance
     *
     * @return ChannelInstance Fetched ChannelInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): ChannelInstance {
        $payload = $this->version->fetch('GET', $this->uri);

        return new ChannelInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['sid']
        );
    }

    /**
     * Delete the ChannelInstance
     *
     * @return bool True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete(): bool {
        return $this->version->delete('DELETE', $this->uri);
    }

    /**
     * Update the ChannelInstance
     *
     * @param array|Options $options Optional Arguments
     * @return ChannelInstance Updated ChannelInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update(array $options = []): ChannelInstance {
        $options = new Values($options);

        $data = Values::of([
            'FriendlyName' => $options['friendlyName'],
            'UniqueName' => $options['uniqueName'],
            'Attributes' => $options['attributes'],
        ]);

        $payload = $this->version->update('POST', $this->uri, [], $data);

        return new ChannelInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['sid']
        );
    }

    /**
     * Access the members
     */
    protected function getMembers(): MemberList {
        if (!$this->_members) {
            $this->_members = new MemberList(
                $this->version,
                $this->solution['serviceSid'],
                $this->solution['sid']
            );
        }

        return $this->_members;
    }

    /**
     * Access the messages
     */
    protected function getMessages(): MessageList {
        if (!$this->_messages) {
            $this->_messages = new MessageList(
                $this->version,
                $this->solution['serviceSid'],
                $this->solution['sid']
            );
        }

        return $this->_messages;
    }

    /**
     * Access the invites
     */
    protected function getInvites(): InviteList {
        if (!$this->_invites) {
            $this->_invites = new InviteList(
                $this->version,
                $this->solution['serviceSid'],
                $this->solution['sid']
            );
        }

        return $this->_invites;
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
        return '[Twilio.Chat.V1.ChannelContext ' . \implode(' ', $context) . ']';
    }
}