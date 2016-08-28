<?php namespace ProcessWire;

class CroppableImage3CropSetting {  //extends WireData {


    const ciVersion = 99;


    public $name = null;
    public $width = null;
    public $height = null;
    public $allowedTemplates = array();

    public function __construct($name, $width, $height, $allowedTemplates) {
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->allowedTemplates = $allowedTemplates;
    }



    /**
     * create a CroppableImage3CropSetting instance from a crop setting line
     * @param  string $cropSettingLine       example: crop-name,200,200,basic-page
     * @return CroppableImage3CropSetting
     */
    public static function createFromString($cropSettingLine) {

        $unsanitizedCropSettingArr = explode(",", $cropSettingLine);

        $cropSettingArr = array();
        foreach ($unsanitizedCropSettingArr as $value) {
            if (($trimmed = trim($value)) !== '') {
                $cropSettingArr[] = $trimmed;
            }
        }

        $allowedTemplates = array_slice($cropSettingArr, 3);
        foreach ($allowedTemplates as $key => $templateName) {
            $allowedTemplates[$key] = trim($templateName);
        }

        $cropSetting = new CroppableImage3CropSetting(
            $name = trim($cropSettingArr[0]),
            $width = (int) trim($cropSettingArr[1]),
            $height = (int) trim($cropSettingArr[2]),
            $allowedTemplates = $allowedTemplates
        );

        return $cropSetting;
    }



    /**
     * check whether a template is allowed for that crop setting
     * @param  string  $templateName
     * @return boolean
     */
    public function isTemplateAllowed($templateName) {

        // if no templates are given in the current crop setting, all templates are allowed
        if (count($this->allowedTemplates) === 0) return true;

        // check if it is contained and only allow it then
        if (in_array($templateName, $this->allowedTemplates)) {
            return true;
        }

        return false;
    }



    /**
     * check whether a string has at least three options, separated by comma
     * Regexr Test: http://regexr.com/39m5s
     * @param  string   $string 'crop-name,200,200 'or 'crop-name,200,200,template1,template2'
     * @return boolean
     */
    public static function isValidString($string) {
        return preg_match("/\s*([a-zA-Z0-9-_])+\s*\,\s*[0-9]+\s*,\s*[0-9]+\s*((,\s*([a-zA-Z\-_0-9])+\s*)*)/", $string) ? true : false;
    }
}



class CroppableImage3CropSettings {

    public $items = array();
    public $names = array();
    public $invalidLines = array();
    public $templateNames = array();
    public $duplicates = array();

    public function __construct($settingString = null) {
        if ($settingString) {
            $settingLines = explode("\n", $settingString);
            foreach($settingLines as $settingLine) {
                $this->addItem($settingLine);
            }
        }
    }



    /**
     * add an item either via string or CroppableImage3CropSetting instance
     * @param string|CroppableImage3CropSetting $setting
     */
    public function addItem($setting) {
        if ($setting instanceof CroppableImage3CropSetting) {
            $this->items[$setting->name] = $setting;
            $this->names[$setting->name] = $setting->name;
        } else if (trim((string) $setting)) {
            if (CroppableImage3CropSetting::isValidString($setting)) {
                $cropSetting = CroppableImage3CropSetting::createFromString($setting);
                if (!in_array($cropSetting->name, $this->names)) {
                    $this->items[$cropSetting->name] = $cropSetting;
                    $this->names[$cropSetting->name] = $cropSetting->name;
                    $this->templateNames = array_merge($this->templateNames, $cropSetting->allowedTemplates);
                } else {
                    $this->duplicates[$cropSetting->name] = trim($setting);
                }
            } else {
                $this->invalidLines[] = trim($setting);
            }
        }
    }



    /**
     * get all crop settings for a specific template
     * @param  string|object(Template) $templateName
     * @return Boolean|CroppableImage3CropSettings   either false if no setting is found for template
     *                                              or a CroppableImage3CropSettings instance with the matching items
     */
    public function getCropSettingsForTemplate($templateName=null) {

        $templateName = (bool) ($templateName instanceof Template) ? $templateName->name : $templateName;
        $cropSettings = new CroppableImage3CropSettings();

        foreach($this->items as $cropSetting) {
            if ($cropSetting->isTemplateAllowed($templateName)) {
                $cropSettings->addItem($cropSetting);
            }
        }
        return count($cropSettings->items > 0) ? $cropSettings->items : false;
    }



    /**
     * get the crop setting by name and for a specific template
     * @param  string $cropSettingName
     * @param  string|object(Template) $templateName
     * @return Boolean|CroppableImage3CropSetting    either false if no setting is found by that name and for template
     *                                              or the crop setting itself
     */
    public function getCropSetting($cropSettingName, $templateName=null) {
        $templateName = (bool) ($templateName instanceof Template) ? $templateName->name : $templateName;
        if (isset($this->items[$cropSettingName]) && null==$templateName) {
            return $this->items[$cropSettingName];
        }
        if (isset($this->items[$cropSettingName]) && is_string($templateName) && $this->items[$cropSettingName]->isTemplateAllowed($templateName)) {
            return $this->items[$cropSettingName];
        }
        return false;
    }

}
