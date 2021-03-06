<?php
namespace NeosRulez\DirectMail\Mailgun\Controller;

/*
 * This file is part of the NeosRulez.DirectMail.Mailgun package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Fusion\View\FusionView;

class MailgunApiController extends ActionController
{

    protected $defaultViewObjectName = FusionView::class;

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\Mailgun\Domain\Service\MailgunService
     */
    protected $mailgunService;

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\Domain\Repository\QueueRepository
     */
    protected $queueRepository;

    /**
     * @Flow\Inject
     * @var \NeosRulez\DirectMail\Domain\Repository\RecipientRepository
     */
    protected $recipientRepository;


    /**
     * @return void
     */
    public function indexAction()
    {
        $queues = $this->queueRepository->findQueuesInProgress();
        $result = [];
        if($queues) {
            foreach ($queues as $queue) {
                $count = $queue->getTosend();
                $queue->total = $count;
                $queue->acceptedEvents = count($this->mailgunService->getAcceptedBySubject($queue->getName()));
                if($queue->acceptedEvents) {
                    $result[] = $queue;
                }
            }
            $this->view->assign('countQueues', count($queues));
        }
        $this->view->assign('queues', $result);
    }

    /**
     * @param \NeosRulez\DirectMail\Domain\Model\Queue $queue
     * @return void
     */
    public function showAction($queue)
    {
        $this->view->assign('mailings', $this->mailgunService->getAllBySubject($queue->getName()));
        $this->view->assign('queue', $queue);
    }

}
