<?php

namespace TNQSoft\CommonBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * NagiosController.
 */
class ExceptionController extends Controller
{
    /**
     * @Route("/error", name="frontend_show_error")
     *
     * @return array
     */
    public function showExceptionAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        return $this->render('TNQSoftCommonBundle:Exception:exception.html.twig',array(
            'exception' => $exception
        ));
    }
}
