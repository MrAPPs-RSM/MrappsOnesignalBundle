<?php

namespace Mrapps\OnesignalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;

class GenerateFilesCommand extends ContainerAwareCommand
{
    private $container;

    public function __construct(Container $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('mrapps:onesignal:generatefiles')
            ->setDescription('Genera i files necessari al bundle MrappsOnesignal nella root WEB')
        ;
    }

    private function refreshFile($filePath, $twigTemplate, $context = array()) {

        if(file_exists($filePath)) unlink($filePath);
        $template = $this->container->get('twig')->loadTemplate($twigTemplate);
        $content = $template->renderBlock('content', $context);

        file_put_contents($filePath, $content);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webDir = $this->container->get('kernel')->getRootDir() . '/../web/';

        //OneSignalSDKWorker.js
        $this->refreshFile($webDir.'OneSignalSDKWorker.js', 'MrappsOnesignalBundle:Assets:OneSignalSDKWorker.html.twig');

        //OneSignalSDKUpdaterWorker.js
        $this->refreshFile($webDir.'OneSignalSDKUpdaterWorker.js', 'MrappsOnesignalBundle:Assets:OneSignalSDKUpdaterWorker.html.twig');

        //manifest.json
        $appName = ($this->container->hasParameter('mrapps_onesignal.parameters.app_name')) ? $this->container->getParameter('mrapps_onesignal.parameters.app_name') : '';
        $gcmSenderId = ($this->container->hasParameter('mrapps_onesignal.web_push.gcm_sender_id')) ? $this->container->getParameter('mrapps_onesignal.web_push.gcm_sender_id') : '';
        $manifestContext = array(
            'app_name' => $appName,
            'gcm_sender_id' => $gcmSenderId,
        );
        $this->refreshFile($webDir.'manifest.json', 'MrappsOnesignalBundle:Assets:manifest.html.twig', $manifestContext);


        $output->writeln("Tutti i files sono stati aggiornati.");
    }
}