<?php

namespace Theme\Services\Notifications\Sms;

use BulkGate\Sdk\Configurator\SmsConfigurator;
use BulkGate\Sdk\Connection\ConnectionStream;
use BulkGate\Sdk\InvalidStateException;
use BulkGate\Sdk\Message\Bulk;
use BulkGate\Sdk\Message\Sms;
use BulkGate\Sdk\MessageSender;
use BulkGate\Sdk\SenderException;
use BulkGate\Sdk\TypeError;
use Exception;

class SmsSender
{

    private MessageSender $gateway;
    private SmsConfigurator $configurator;

    /**
     * @throws InvalidStateException
     */
    public function __construct()
    {
        $connection = new ConnectionStream(
            application_id: config('sms.app_id'),
            application_token: config('sms.app_token'),
        );

        $this->gateway = new MessageSender($connection);
        $this->gateway->setDefaultCountry('sk');

        $this->configurator = new SmsConfigurator();
        $this->configurator->mobileConnect(config("sms.android_app_key"));
        $this->configurator->unicode(false);
    }

    /**
     * @throws TypeError
     * @throws Exception
     */
    public function send(string $messageContent, array|string $phoneNumbers): bool
    {
        $message = new Bulk();

        if (!is_array($phoneNumbers)) {
            $phoneNumbers = [$phoneNumbers];
        }

        foreach ($phoneNumbers as $phoneNumber) {
            $currentMessage = new Sms($phoneNumber, $messageContent);
            $this->configurator->configure($currentMessage);
            $message[] = $currentMessage;
        }

        try {
            $this->gateway->send($message);
            return true;
        } catch (SenderException $e) {
            main()->log()->error("SMS Brana | Chyba: " . json_encode($e));
            return false;
        }
    }
}