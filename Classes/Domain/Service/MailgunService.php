<?php
namespace NeosRulez\DirectMail\Mailgun\Domain\Service;

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

use Mailgun\Mailgun;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Hydrator\ArrayHydrator;

/**
 *
 * @Flow\Scope("singleton")
 */
class MailgunService {

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }

    /**
     * @return object
     */
    public function create():object
    {
        $mg = $this->settings['server'] == 'EU' ? Mailgun::create($this->settings['privateApiKey'], 'https://api.eu.mailgun.net') : Mailgun::create($this->settings['privateApiKey']);
        return $mg;
    }

    /**
     * @param string $subject
     * @return array
     */
    public function getAcceptedBySubject($subject):array
    {
        $mg = $this->create();
        $events = $mg->events()->get($this->settings['domain']);
        $acceptedEvents = [];
        if($events) {
            foreach ($events->getItems() as $item) {
                $event = $item->getEvent();
                $mailSubject = $item->getMessage()['headers']['subject'];
                if($event == 'accepted') {
                    if($mailSubject == $subject) {
                        $acceptedEvents[] = $item;
                    }
                }
            }
        }
        return $acceptedEvents;
    }

    /**
     * @param string $subject
     * @return array
     */
    public function getAllBySubject($subject):array
    {
        $mg = $this->create();
        $events = $mg->events()->get($this->settings['domain']);
        $acceptedEvents = [];
        if($events) {
            foreach ($events->getItems() as $item) {
                $mailSubject = $item->getMessage()['headers']['subject'];
                if($mailSubject == $subject) {
                    $acceptedEvents[] = $item;
                }
            }
        }
        return $acceptedEvents;
    }

}
