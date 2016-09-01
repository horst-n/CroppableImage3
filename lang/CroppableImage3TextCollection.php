<?php namespace ProcessWire;

class CroppableImage3TextCollection extends WireData {

    protected $textCollection = array();

    public function combine($textCollection) {
        return array_merge($this->textCollection, $textCollection);
    }

    public function __construct() {

        require_once(dirname(__FILE__) . '/../classes/CroppableImage3Helpers.class.php');
        $globalOptions = CroppableImage3Helpers::arrayToObject(CroppableImage3Helpers::getGlobalConfigSettings(true));
        $labelTextType = isset($globalOptions->labelTextType) ? $globalOptions->labelTextType : 'legacy';

        $subCollection = array(
            'legacy'  => array(
                'confirmCropText' => $this->_('Wow, that looks great!'),
                'confirmCropTextError' => $this->_('Uh! An Error has occured: %s'),
                'cropAgainText' => $this->_('Not happy? Crop again!'),
                'cropAgainTextError' => $this->_('Do you want try again?'),
                'applyCropText' => $this->_('Crop and go'),
                'descriptionText' => $this->_('Cropped image will be resized to %s pixels. Click and drag the cropped area with your cursor.'),
            ),
            'serious' => array(
                'confirmCropText' => $this->_('Accept!'),
                'confirmCropTextError' => $this->_('An Error has occured: %s'),
                'cropAgainText' => $this->_('Redo Crop'),
                'cropAgainTextError' => $this->_('Do you want try it again?'),
                'applyCropText' => $this->_('proceed Crop'),
                'descriptionText' => $this->_('Cropped image will be resized to %s pixels. Click and drag the cropped area with your cursor.'),
            ),
            'brave'   => array(
                'confirmCropText' => $this->_('Uhm - yeah, go on!'),
                'confirmCropTextError' => $this->_('Sh*t! An Error has occured: %s'),
                'cropAgainText' => $this->_('Go back, I can do it better!'),
                'cropAgainTextError' => $this->_('Do you want try again?'),
                'applyCropText' => $this->_('Crop it!'),
                'descriptionText' => $this->_('Cropped image will be resized to %s pixels. Click and drag the cropped area with your cursor.'),
            )
        );

        $this->textCollection = array(

         // identify key
            'CroppableImage3TextCollectionVersion' => '0.0.2',

         // FieldtypeCroppableImage3
            'cropSettings_invalidLine' => $this->_("Crop Settings: This line is not a valid crop setting: '%s'"),
            'cropSettings_invalidTemplates' => $this->_("Crop Settings: The following templates have not been found: '%s'"),
            'cropSettings_mustbeunique' => $this->_("Crop setting names must be unique, '%s' is already defined: '%s'"),
            'cropSettings_RestoreMsg' => $this->_("Because of invalid Crop-Settings that may result in unwanted removal of variations, we restore the previously used settings now!"),
            'cropSettings_NoSetting4Template' => $this->_("There is no crop setting for the template '%s' called '%s'"),
            'cropSettings_widthHeightNotFound' => $this->_("Width and height not found for crop setting '%s'"),

         // InputfieldCroppableImage3
            'predefinedCrops' => $this->_('predefined Crops'),
            'cropSetting_toLarge' => $this->_('This crop setting (%s) is larger than the source image (%s) and upscaling is not allowed.'),
            'jsCropInfo_edit' => $this->_('edit'),
            'jsCropInfo_emptyFields' => $this->_('Some input fields are empty'),
            'fieldCropSetting_Label' => $this->_('Crop Settings'),
            'fieldCropSetting_Description' => $this->_('Enter all crop dimensions, one on each line in this format: name,width,height. Few examples: "landscape,900,600" or "portrait,200,600". Optionally, you can bind a cropsetting to only explicitly named templates by giving the templatename(s) as last parameter(s). Examples: "panorama,1200,600,home" or "square,300,300,basic-page,home"'),

         // ProcessCroppableImage3
            'confirmCropText' => $subCollection[$labelTextType]['confirmCropText'],
            'confirmCropTextError' => $subCollection[$labelTextType]['confirmCropTextError'],
            'cropAgainText' => $subCollection[$labelTextType]['cropAgainText'],
            'cropAgainTextError' => $subCollection[$labelTextType]['cropAgainTextError'],
            'applyCropText' => $subCollection[$labelTextType]['applyCropText'],
            'descriptionText' => $subCollection[$labelTextType]['descriptionText'],

            'sharpeningText' => $this->_('Sharpen'),
            'qualityText' => $this->_('Quality'),
            'showPreviewText' => $this->_('Show Preview?'),
            'invalidFieldText' => $this->_('This page only works when you come from image field.'),
            'noValidSuffix' => $this->_("We have no valid Suffix (%s) or it's the wrong page-template (%s)!"),
            'invalidPageID' => $this->_('Invalid Page-ID'),
            'imageSizerResizeFailed' => $this->_('ImageSizer::resize(%s, %s) failed for %s.%s Click here to close the Editor!'),

         // FieldtypeCroppableImage3ConfAdaptor
            'fieldsetDescription1' => $this->_('Here you can select which type of Buttontexts you prefer'),
            'labelTextType_Description' => $this->_('Buttontext Style: legacy & funny | serious & boring | brave & unknown'),
            'labelTextType_Label' => $this->_('Buttontext Style'),
            'labelTextType_Notes' => $this->_('This defines which texts are displayed on buttons. For example: Wow, that looks great! | Accept! | Uhm - yeah, go on!'),
            'confTexttypeLegacy' => $this->_("I like it funny"),
            'confTexttypeSerious' => $this->_("I am serious"),
            'confTexttypeBrave' => $this->_("I'm brave, surprise me"),

            'fieldset_quality_sharpening_Description' => $this->_('Here you can set sitewide options for Quality and Sharpening. Per default there are selections available in the crop editor, but you can disable them here and define what should be used instead!'),
            'manualSelectionDisabled_Label' => $this->_('Globally disable the usage of DropDown-Selects for Quality & Sharpening in the CropEditor!'),
            'manualSelectionDisabled_Notes' => $this->_('Instead define them here or use the ImagesizerEngines default values'),
            'useImageEngineDefaults_Label' => $this->_('Use the ImagesizerEngines default values for Quality & Sharpening!'),
            'optionQuality_Description' => $this->_('quality: 1-100 where higher is better but bigger'),
            'optionQuality_Label' => $this->_('Global Setting for Quality'),
            'optionSharpening_Description' => $this->_('sharpening: none | soft | medium | strong'),
            'optionSharpening_Label' => $this->_('Global Setting for Sharpening'),

            'dothedishes_delete_noSuccess' => $this->_('ERROR: Removing Imagevariations was not successfully finished. Refer to the errorlog for more details.'),
            'dothedishes_delete_Success' => $this->_('SUCCESS! All Imagevariations are removed.'),
            'dothedishes_noDelete_noSuccess' => $this->_('ERROR: Could not find and list all Pages containing Imagevariations. Refer to the errorlog for more details.'),
            'dothedishes_noDelete_Success' => $this->_('SUCCESS! Found and listed all Pages with Imagevariations.'),
        );
    }
}
