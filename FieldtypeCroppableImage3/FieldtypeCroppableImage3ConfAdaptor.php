<?php namespace ProcessWire;

class FieldtypeCroppableImage3ConfAdaptor extends Wire {

    const ciVersion = 99;

    static protected $sharpeningValues = array('none', 'soft', 'medium', 'strong');


    public function getConfig(array $data) {

        if(!isset($data['manualSelectionDisabled'])) $data['manualSelectionDisabled'] = false;
        if(!isset($data['useImageEngineDefaults'])) $data['useImageEngineDefaults'] = false;
        if(!isset($data['optionSharpening'])) $data['optionSharpening'] = 'soft';
        if(!isset($data['optionQuality'])) $data['optionQuality'] = 90;

        require_once(dirname(__FILE__) . '/../classes/CroppableImage3Helpers.class.php');
        $modules = wire('modules');
        $form = new InputfieldWrapper();

        $fieldset = $modules->get('InputfieldFieldset');
        $fieldset->label = 'Quality & Sharpening';
        $fieldset->attr('name+id', '_quality_sharpening');
        $fieldset->description = $this->_('Here you can set sitewide options for Quality and Sharpening. Per default there are selections available in the crop editor, but you can disable them here and define what should be used instead!');
        $fieldset->collapsed = Inputfield::collapsedNo;

            $field = $modules->get('InputfieldCheckbox');
            $field->attr('name+id', 'manualSelectionDisabled');
            $field->label = $this->_('Globally disable the usage of DropDown-Selects for Quality & Sharpening in the CropEditor!');
            $field->notes = $this->_('Instead define them here or use the ImagesizerEngines default values');
            $field->attr('value', 1);
            $field->attr('checked', ($data['manualSelectionDisabled'] ? 'checked' : ''));
            $field->columnWidth = 65;
            $fieldset->add($field);

            $field = $modules->get('InputfieldSelect');
            $field->label = $this->_('Global Setting for Sharpening');
            $field->attr('name+id', 'optionSharpening');
            if(is_numeric($data['optionSharpening']) && isset(self::$sharpeningValues[intval($data['optionSharpening'])])) {
                $value = $data['optionSharpening'];
            } elseif(is_string($data['optionSharpening']) && in_array($data['optionSharpening'], self::$sharpeningValues)) {
                $flippedA = array_flip(self::$sharpeningValues);
                $value = strval($flippedA[$data['optionSharpening']]);
            } else {
                $value = '1';
            }
            $field->attr('value', intval($value));
            $field->addOptions(self::$sharpeningValues);
            $field->description = $this->_('sharpening: none | soft | medium | strong');
            $field->columnWidth = 35;
            $field->showIf = "manualSelectionDisabled=1,useImageEngineDefaults=0";
            $fieldset->add($field);

            $field = $modules->get('InputfieldCheckbox');
            $field->attr('name+id', 'useImageEngineDefaults');
            $field->label = $this->_('Use the ImagesizerEngines default values for Quality & Sharpening!');
            $field->attr('value', 1);
            $field->attr('checked', ($data['useImageEngineDefaults'] ? 'checked' : ''));
            $field->showIf = "manualSelectionDisabled=1";
            $ImageSizer = new ImageSizer();
            $engines = array_merge($ImageSizer->getEngines(), array('ImageSizerEngineGD'));
            $a = array();
            foreach($engines as $e) {
                $mcd = 'ImageSizerEngineGD' == $e ? wire('config')->imageSizerOptions : $modules->getModuleConfigData($e);
                $a[] = ' [&nbsp;' . implode('&nbsp;|&nbsp;', array($e, $mcd['quality'], $mcd['sharpening'])) . '&nbsp;] ';
            }
            $s = implode(' - ', $a);
            if(!empty($s)) $field->notes = $s;
            //$this->_('Is defined and can be changed in the Engines module config pages!')
            $field->columnWidth = 65;
            $fieldset->add($field);

            $field = $modules->get('InputfieldInteger');
            $field->label = $this->_('Global Setting for Quality');
            $field->attr('name+id', 'optionQuality');
            $field->attr('value', ($data['optionQuality']>0 && $data['optionQuality']<=100 ? $data['optionQuality'] : 90));
            $field->description = $this->_('quality: 1-100 where higher is better but bigger');
            $field->columnWidth = 35;
            $field->showIf = "manualSelectionDisabled=1,useImageEngineDefaults=0";
            $fieldset->add($field);

        $form->add($fieldset);

        return $form;
    }



    public function doTheDishes($deleteVariations=false) {
        $errors = array();
        $success = false;
        try {
            $success = $this->removeAllVariations($deleteVariations);

        } catch(Exception $e) {
            $errors[] = $e->getMessage();
        }
        if($success) {
            $note = $deleteVariations ?
                $this->_('SUCCESS! All Imagevariations are removed.') :
                $this->_('SUCCESS! Found and listed all Pages with Imagevariations.');
            $this->message($note);

        } else {
            $note = $deleteVariations ?
                $this->_('ERROR: Removing Imagevariations was not successfully finished. Refer to the errorlog for more details.') :
                $this->_('ERROR: Could not find and list all Pages containing Imagevariations. Refer to the errorlog for more details.');
            $this->error($note);
        }
        return $note;
    }


    private function removeAllVariations($deleteVariations=false) {
        $stack = new filo();
        $stack->push(1);
        while($id = $stack->pop()) {
            set_time_limit(intval(15));
            // get the page
            $page = wire('pages')->get($id);
            if(0==$page->id) continue;
            // add children to the stack
            foreach($page->children('include=all') as $child) {
                $stack->push($child->id);
            }
            // iterate over the fields
            foreach($page->fields as $field) {
                if(! $field->type instanceof FieldtypeImage) {
                    continue;
                }
                // get the images
                $imgs = $page->{$field->name};
                $count = count($imgs);
                if(0==$count) continue;
                $this->message('- found page: ' . $page->title . ' - with imagefield: ' . $field->name . ' - count: ' . $count);
                foreach($imgs as $img) {
                    if(true===$deleteVariations) {
                        $this->message(' REMOVED! ');
                        #$img->removeVariations();
                    }
                }
            }
            wire('pages')->uncache($page);
        }
        return true;
    }

}

if(!class_exists('ProcessWire\\filo')) {
    /** @shortdesc: Stack, First In - Last Out  **/
    class filo {

        /** @private **/
        var $elements;
        /** @private **/
        var $debug;

        /** @private **/
        function filo($debug=false) {
            $this->debug = $debug;
            $this->zero();
        }

        /** @private **/
        function push($elm) {
            array_push($this->elements, $elm);
            if($this->debug) echo "<p>filo->push(".$elm.")</p>";
        }

        /** @private **/
        function pop() {
            $ret = array_pop( $this->elements );
            if($this->debug) echo "<p>filo->pop() = $ret</p>";
            return $ret;
        }

        /** @private **/
        function zero() {
            $this->elements = array();
            if($this->debug) echo "<p>filo->zero()</p>";
        }
    }
} // end class FILO

