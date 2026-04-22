<?php

class Editor {
    var $InstanceName ;
    var $Width ;
    var $Height ;
    var $Value ;

    function __construct( $instanceName ) {
        $this->InstanceName	= $instanceName ;
        $this->Width		= '100%' ;
        $this->Height		= '300' ;
        $this->Value		= '' ;
    }

    function AddGUI() {
        return $this->Textarea() ;
    }

    // Отключенный редактор
    function Textarea() {
        if ( strpos( $this->Width, '%' ) === false )
            $WidthCSS = $this->Width . 'px' ;
        else
            $WidthCSS = $this->Width ;

        if ( strpos( $this->Height, '%' ) === false )
            $HeightCSS = $this->Height . 'px' ;
        else
            $HeightCSS = $this->Height ;
        return "<div style=\"width: {$WidthCSS}; height: {$HeightCSS};overflow-y: scroll\">{$this->Value}</div><small class=\"text-muted\">".__('Текст не редактируется')."</small>";
    }
}

?>
