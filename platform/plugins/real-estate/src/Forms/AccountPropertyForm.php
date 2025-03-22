<?php

namespace Srapid\RealEstate\Forms;

use Assets;
use Srapid\RealEstate\Forms\Fields\CustomEditorField;
use Srapid\RealEstate\Forms\Fields\MultipleUploadField;
use Srapid\RealEstate\Http\Requests\AccountPropertyRequest;
use Srapid\RealEstate\Models\Property;
use RealEstateHelper;

class AccountPropertyForm extends PropertyForm
{

    /**
     * @return mixed|void
     * @throws \Throwable
     */
    public function buildForm()
    {
        parent::buildForm();

        Assets::addScriptsDirectly('vendor/core/core/base/libraries/tinymce/tinymce.min.js')
            ->addScriptsDirectly('vendor/core/plugins/real-estate/js/account-property.js');

        if (!$this->formHelper->hasCustomField('customEditor')) {
            $this->formHelper->addCustomField('customEditor', CustomEditorField::class);
        }

        if (!$this->formHelper->hasCustomField('multipleUpload')) {
            $this->formHelper->addCustomField('multipleUpload', MultipleUploadField::class);
        }

        $this
            ->setupModel(new Property)
            ->setFormOption('template', 'plugins/real-estate::account.forms.base')
            ->setFormOption('enctype', 'multipart/form-data')
            ->setValidatorClass(AccountPropertyRequest::class)
            ->setActionButtons(view('plugins/real-estate::account.forms.actions')->render())
            ->remove('is_featured')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('images[]')
            ->remove('never_expired')
            ->modify('auto_renew', 'onOff', [
                'label'         => trans('plugins/real-estate::property.renew_notice', ['days' => RealEstateHelper::propertyExpiredDays()]),
                'label_attr'    => ['class' => 'control-label'],
                'default_value' => false,
            ], true)
            ->remove('author_id')
            ->addAfter('description', 'content', 'customEditor', [
                'label'      => trans('core/base::forms.content'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'rows' => 4,
                ],
            ])
            ->addAfter('content', 'watermark_toggle', 'html', [
                'html' => '
                <div class="form-group mb-3 watermark-toggle-wrapper">
                    <label class="control-label">Marca d\'água nas imagens</label>
                    <div class="onoffswitch-container">
                        <div class="onoffswitch">
                            <input type="checkbox" name="watermark_toggle" class="onoffswitch-checkbox" id="watermark-toggle" ' . (setting('media_watermark_enabled', false) ? 'checked' : '') . '>
                            <label class="onoffswitch-label" for="watermark-toggle">
                                <span class="onoffswitch-inner"></span>
                                <span class="onoffswitch-switch"></span>
                            </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>',
            ])
            ->addAfter('watermark_toggle', 'images', 'multipleUpload', [
                'label'      => trans('plugins/real-estate::property.form.images'),
                'label_attr' => ['class' => 'control-label'],
            ]);
    }
}
