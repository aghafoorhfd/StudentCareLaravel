<?php
namespace Modules\Template\Blocks;
class Text extends BaseBlock
{
    public function __construct()
    {
        $this->setOptions([
            'settings' => [
                [
                    'id'    => 'content',
                    'type'  => 'editor',
                    'label' => __('Editor')
                ],
                [
                    'id'        => 'class',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Wrapper Class (opt)')
                ],

            ]
        ]);
    }

    public function getName()
    {
        return __('Text');
    }

    public function content($model = [])
    {
        return view('Template::frontend.blocks.text', $model);
    }
}
