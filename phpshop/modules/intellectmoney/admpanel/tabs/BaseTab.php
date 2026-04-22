<?php 

abstract class BaseTab
{
    /**
     * ������� �������.
     */
    protected $data;

    function __construct($userSettings)
    {
        global $PHPShopGUI;
        $this->data = '';
        $this->appendJs('main.js');
    }

    public function getTab()
    {
        return [
            'Undefined', 
            $this->data, 
            true,
        ];
    }

    protected function appendJs($fileName) {
        $this->data .= '<script src="/phpshop/modules/intellectmoney/admpanel/js/'.$fileName.'"></script>';
    }

    protected function generatetHeader($text) {
        $this->data .= '<h5 class="text-muted">'.$text.'</h5>';
    }

    protected function generateSelect($label, $name, $values)
    {
        global $PHPShopGUI;
        foreach($values as $value) {
            $resultValues[] = [
                $value['title'], 
                $value['value'], 
                $value['selected'] ? 'selected' : false, 
            ];
        }

        $this->data .= $PHPShopGUI->setField($label, $PHPShopGUI->setSelect($name, $resultValues, 300, false, false, false, false , 1, false, $name));
    }

    protected function generateInput($label, $name, $value)
    {
        global $PHPShopGUI;
        $this->data .= $PHPShopGUI->setField($label, $PHPShopGUI->setInputText('', $name, $value, 300));
    }

    protected function generateCheckbox($label, $name, $value)
    {
        global $PHPShopGUI;
        $this->data .= $PHPShopGUI->setField($label, $PHPShopGUI->setCheckbox($name, '', '', $value ? 1 : 0));
    }
}