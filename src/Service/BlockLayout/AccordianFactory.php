<?php
namespace PageBlocks\Service\BlockLayout;

use Interop\Container\ContainerInterface;
use PageBlocks\Site\BlockLayout\Accordian;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AccordianFactory implements FactoryInterface
{
    /**
     * Create the Accordian block layout service.
     *
     * @param ContainerInterface $serviceLocator
     * @return Accordian
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $htmlPurifier = $serviceLocator->get('Omeka\HtmlPurifier');
        return new Accordian($htmlPurifier);
    }
}
?>
