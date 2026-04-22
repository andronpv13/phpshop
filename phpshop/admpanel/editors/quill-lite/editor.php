<?php

class Editor {

    var $InstanceName;
    var $Width;
    var $Height;
    var $Value;

    function __construct($instanceName) {
        $this->InstanceName = $instanceName;
        $this->Width = '100%';
        $this->Height = '300';
        $this->Value = '';
    }

    function AddGUI() {
        return $this->Textarea();
    }

    // Отключенный редактор
    function Textarea() {
        global $PHPShopSystem;

        if (strpos($this->Width, '%') === false)
            $WidthCSS = $this->Width . 'px';
        else
            $WidthCSS = $this->Width;

        if (strpos($this->Height, '%') === false)
            $HeightCSS = $this->Height . 'px';
        else
            $HeightCSS = $this->Height;

        $html = '<link href="./editors/quill/quill.bubble.css" rel="stylesheet" />

        <script src="./editors/quill/quill.min.js"></script>
        <style>
  body > #standalone-container {
    margin: 50px auto;
    max-width: ' . $WidthCSS . ';
  }
  #editor_' . $this->InstanceName . ' {
    height: ' . $HeightCSS . ';
  }

</style>
        ';

        $html .= '<div>
  <div id="editor_' . $this->InstanceName . '">' . $this->Value . '</div>
  <textarea id="editor_src_' . $this->InstanceName . '" name="' . $this->InstanceName . '" id="" class="hidden-edit hide">' . $this->Value . '</textarea>
</div>';

        $html .= "<script>
            
var toolbarOptions = [
  ['bold', 'italic'],        // toggled buttons

  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

  [{ 'color': [] }],          // dropdown with defaults from theme
  [{ 'align': [] }], ['link', 'image'],

  ['clean']                                         // remove formatting button
];

  var quill_" . $this->InstanceName . " = new Quill('#editor_" . $this->InstanceName . "', {
    modules: {
      toolbar: {
           container: toolbarOptions,
           handlers: { image: quill_img_handler }
                },
       },
    placeholder: '',
    theme: 'bubble'
  });
  
  
   $('[name=\"editID\"], [name=\"saveID\"], [type=\"submit\"]').on('click', function() {
      $('#editor_src_" . $this->InstanceName . "').val($('#editor_" . $this->InstanceName ." .ql-editor').html());
   });
   
   function quill_img_handler() {
      $('.elfinder-modal-content').attr('data-option','return=". $this->InstanceName . "');
      $('#elfinderModal').modal('show');
   }
</script>";

        return $html;
    }

}

?>