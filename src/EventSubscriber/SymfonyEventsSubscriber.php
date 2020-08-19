<?php
namespace App\EventSubscriber;

use App\Factory\JsonResponseFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SymfonyEventsSubscriber implements EventSubscriberInterface
{

    /**
     * @var JsonResponseFactory
     */
    private JsonResponseFactory $responseFactory;

    public function __construct(JsonResponseFactory $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                'convertErrorToJson',
            ],
        ];
    }

    /**
     * @param ExceptionEvent $event
     */
    public function convertErrorToJson(ExceptionEvent $event): void
    {
        if (in_array('application/json', $event->getRequest()->getAcceptableContentTypes())) {
            $event->setResponse($this->responseFactory->createErrorResponse($event->getThrowable()));
        }
    }
}
