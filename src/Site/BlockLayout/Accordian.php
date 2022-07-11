<?php
namespace PageBlocks\Site\BlockLayout;

use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Omeka\Entity\SitePageBlock;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element;
use Laminas\Form\Fieldset;   // to remove
use Laminas\Form\Form;
use Laminas\View\Renderer\PhpRenderer;


class Accordian extends AbstractBlockLayout
{
    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;

    public function __construct(HtmlPurifier $htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    public function getLabel()
    {
        return 'Accordian section'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $heading = isset($data['heading']) ? $this->htmlPurifier->purify($data['heading']) : '';
        $data['heading'] = $heading;
        $data['initialState'] = isset($data['initialState']) ? $this->htmlPurifier->purify($data['initialState']) : 'closed';
        $html = isset($data['html']) ? $this->htmlPurifier->purify($data['html']) : '';
        $data['html'] = $html;
        $data['divclass'] = isset($data['divclass']) ? $this->htmlPurifier->purify($data['divclass']) : '';;
        $block->setData($data);
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
        SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {
        $fieldset = new Fieldset();
        $fieldset->add([
            'name' => 'o:block[__blockIndex__][o:data][heading]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Block title:', // @translate
            ],
        ]);
        $fieldset->add([
            'name' => 'o:block[__blockIndex__][o:data][initial_state]',
            'type' => Element\Radio::class,
            'options' => [
                'label' => 'Initial state:', // @translate
                'value_options' => [
                    'closed' => 'closed', // @translate
                    'open' => 'open', // @translate
                ],
            ],
        ]);
        $fieldset->add([
            'name' => 'o:block[__blockIndex__][o:data][html]',
            'type' => Element\Textarea::class,
            'attributes' => [
                'class' => 'block-html full wysiwyg',
            ],
        ]);
        $fieldset->add([
            'name' => 'o:block[__blockIndex__][o:data][divclass]',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'CSS class:', // @translate
                'info' => 'Optional CSS class for styling HTML.', // @translate
            ],
        ]);

        $dataForm = [];
        if ($block) {
            foreach ($block->data() as $key => $value) {
                $dataForm['o:block[__blockIndex__][o:data][' . $key . ']'] = $value;
            }
        } else {
            $dataForm['o:block[__blockIndex__][o:data][initial_state]'] = 'closed';
        }
        $fieldset->populateValues($dataForm);

        return $view->formCollection($fieldset);

/*
        $form = new Form();
        $heading = new Element\Text("o:block[__blockIndex__][o:data][heading]");
        $heading->setOptions([
            'label' => 'Section heading:' // @translate
        ]);
        $initialState = new Element\Radio("o:block[__blockIndex__][o:data][initial_state]");
        $initialState->setOptions([
            'label' => 'Initial state:', // @translate
            'value_options' => [
                'closed' => 'closed', // @translate
                'open' => 'open' // @translate
            ]
        ]);
        $html = new Element\Textarea("o:block[__blockIndex__][o:data][html]");
        $html->setAttribute('class', 'block-html full wysiwyg');
        $divClass = new Element\Text("o:block[__blockIndex__][o:data][divclass]");
        $divClass->setOptions([
            'label' => 'Class', // @translate
            'info' => 'Optional CSS class for styling HTML.', // @translate
        ]);
        if ($block) {
            $heading->setValue($block->dataValue('heading'));
            $initialState->setValue($block->dataValue('initialState'));
            $html->setValue($block->dataValue('html'));
            $divClass->setValue($block->dataValue('divclass'));
        }
        $form->add($heading);
        $form->add($initialState);
        $form->add($html);
        $form->add($divClass);

        return $view->formCollection($form);
*/
    }

    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $contentBlock = $block->dataValue('html', '');
        if ($block->dataValue('initial_state', 'closed') === 'open') {
            $htmlBlock = '<details open="true">' . $contentBlock . '</details>';
        } else {
            $htmlBlock = '<details>' . $contentBlock . '</details>';
        }
        $divClass = $view->escapeHtml($block->dataValue('divclass'));
        if (!empty($divClass)) {
            //wrap HTML in div with specified class, if present
            $htmlFinal = '<div class="' . $divClass . '">';
            $htmlFinal .= $htmlBlock;
            $htmlFinal .= '</div>';
        } else {
            $htmlFinal = $htmlBlock;
        }

        return $htmlFinal;
    }

    public function getFulltextText(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        return strip_tags($this->render($view, $block));
    }
}
?>
