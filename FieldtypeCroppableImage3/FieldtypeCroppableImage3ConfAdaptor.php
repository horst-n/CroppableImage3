<?php namespace ProcessWire;

class FieldtypeCroppableImage3ConfAdaptor extends Wire {

    const ciVersion = '1.2.0';

    static protected $sharpeningValues = array('none', 'soft', 'medium', 'strong');

    protected $textCollection = array();

    protected function getText($key, $default = '(N/A)') {
        if(!isset($this->textCollection['CroppableImage3TextCollectionVersion'])) {
            require_once(dirname(__FILE__) . '/../lang/CroppableImage3TextCollection.php');
            $c = new CroppableImage3TextCollection();
            $this->textCollection = $c->combine($this->textCollection);
        }
        return isset($this->textCollection[$key]) ? $this->textCollection[$key] : $default;
    }

    public function getConfig(array $data) {

        if(!isset($data['manualSelectionDisabled'])) $data['manualSelectionDisabled'] = false;
        if(!isset($data['useImageEngineDefaults'])) $data['useImageEngineDefaults'] = false;
        if(!isset($data['optionSharpening'])) $data['optionSharpening'] = 'soft';
        if(!isset($data['optionQuality'])) $data['optionQuality'] = 90;
        if(!isset($data['labelTextType'])) $data['labelTextType'] = 'legacy';  // @apeisa: hope this helps to get over faster! :)

        require_once(dirname(__FILE__) . '/../classes/CroppableImage3Helpers.class.php');
        $modules = wire('modules');
        $form = new InputfieldWrapper();

        $fieldset = $modules->get('InputfieldFieldset');
        $fieldset->label = 'Button Labeltext Type';
        $fieldset->attr('name+id', '_button_texts');
        $fieldset->description = $this->getText('fieldsetDescription1');
        $fieldset->collapsed = Inputfield::collapsedNo;
            $field = $modules->get('InputfieldSelect');
            $field->label = $this->getText('labelTextType_Label');
            $field->attr('name+id', 'labelTextType');
            $selects = array(
                'legacy'  => $this->getText('confTexttypeLegacy'),
                'serious' => $this->getText('confTexttypeSerious'),
                'brave'   => $this->getText('confTexttypeBrave')
            );
            $field->addOptions($selects);
            $field->attr('value', $data['labelTextType']);
            $field->description = $this->getText('labelTextType_Description');
            $field->notes = $this->getText('labelTextType_Notes');
            $fieldset->add($field);
        $form->add($fieldset);

        $fieldset = $modules->get('InputfieldFieldset');
        $fieldset->label = 'Quality & Sharpening';
        $fieldset->attr('name+id', '_quality_sharpening');
        $fieldset->description = $this->getText('fieldset_quality_sharpening_Description');
        $fieldset->collapsed = Inputfield::collapsedNo;

            $field = $modules->get('InputfieldCheckbox');
            $field->attr('name+id', 'manualSelectionDisabled');
            $field->label = $this->getText('manualSelectionDisabled_Label');
            $field->notes = $this->getText('manualSelectionDisabled_Notes');
            $field->attr('value', 1);
            $field->attr('checked', ($data['manualSelectionDisabled'] ? 'checked' : ''));
            $field->columnWidth = 65;
            $fieldset->add($field);

            $field = $modules->get('InputfieldSelect');
            $field->label = $this->getText('optionSharpening_Label');
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
            $field->description = $this->getText('optionSharpening_Description');
            $field->columnWidth = 35;
            $field->showIf = "manualSelectionDisabled=1,useImageEngineDefaults=0";
            $fieldset->add($field);

            $field = $modules->get('InputfieldCheckbox');
            $field->attr('name+id', 'useImageEngineDefaults');
            $field->label = $this->getText('useImageEngineDefaults_Label');
            $field->attr('value', 1);
            $field->attr('checked', ($data['useImageEngineDefaults'] ? 'checked' : ''));
            $field->showIf = "manualSelectionDisabled=1";
            $ImageSizer = new ImageSizer();
            $engines = array_merge($ImageSizer->getEngines(), array('ImageSizerEngineGD'));
            $a = array();
            $defaultQuality = isset($this->wire->config->imageSizerOptions['quality']) ? $this->wire->config->imageSizerOptions['quality'] : 90;
            $defaultSharpening = isset($this->wire->config->imageSizerOptions['sharpening']) ? $this->wire->config->imageSizerOptions['sharpening'] : 'soft';
            foreach($engines as $e) {
                $mcd = 'ImageSizerEngineGD' == $e ? $this->wire('config')->imageSizerOptions : $modules->getModuleConfigData($e);
                $mcd['quality'] = isset($mcd['quality']) ? $mcd['quality'] : $defaultQuality;
                $mcd['sharpening'] = isset($mcd['sharpening']) ? $mcd['sharpening'] : $defaultQuality;
                $a[] = ' [&nbsp;' . implode('&nbsp;|&nbsp;', array($e, $mcd['quality'], $mcd['sharpening'])) . '&nbsp;] ';
            }
            $s = implode(' - ', $a);
            if(!empty($s)) $field->notes = $s;
            //$this->_('Is defined and can be changed in the Engines module config pages!')
            $field->columnWidth = 65;
            $fieldset->add($field);

            $field = $modules->get('InputfieldInteger');
            $field->label = $this->getText('optionQuality_Label');
            $field->attr('name+id', 'optionQuality');
            $field->attr('value', ($data['optionQuality']>0 && $data['optionQuality']<=100 ? $data['optionQuality'] : 90));
            $field->description = $this->getText('optionQuality_Description');
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
                $this->getText('dothedishes_delete_Success') :
                $this->getText('dothedishes_noDelete_Success');
            $this->message($note);

        } else {
            $note = $deleteVariations ?
                $this->getText('dothedishes_delete_noSuccess') :
                $this->getText('dothedishes_noDelete_noSuccess');
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

        var $elements;
        var $debug;

        function __construct($debug=false) {
            $this->debug = $debug;
            $this->zero();
        }

        function filo($debug=false) {
            $this->__construct($debug);
        }

        function push($elm) {
            array_push($this->elements, $elm);
            if($this->debug) echo "<p>filo->push(".$elm.")</p>";
        }

        function pop() {
            $ret = array_pop( $this->elements );
            if($this->debug) echo "<p>filo->pop() = $ret</p>";
            return $ret;
        }

        function zero() {
            $this->elements = array();
            if($this->debug) echo "<p>filo->zero()</p>";
        }
    }
} // end class FILO

