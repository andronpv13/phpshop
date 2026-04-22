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

        $html = '
        <link href="./editors/quill/quill.snow.css" rel="stylesheet" />

        <script src="./editors/quill/quill.min.js"></script>
        <style>
  body > #standalone-container, {
    margin: 50px auto;
  }
  #editor_' . $this->InstanceName . ',#editor_src_' . $this->InstanceName . ' {
    height: ' . $HeightCSS . ';
    width: ' . $WidthCSS . ';    
  }

</style>
        ';

        $html .= '<div>
  <div id="editor_' . $this->InstanceName . '">' . $this->Value . '</div>
  <textarea id="editor_src_' . $this->InstanceName . '" name="' . $this->InstanceName . '" class="hidden-edit hide form-control quill-code">' . $this->Value . '</textarea>
</div>';

        $html .= "<script>
            
var toolbarOptions = [
  ['bold', 'italic'],        // toggled buttons
  [{ 'list': 'ordered'}, { 'list': 'bullet' }],

  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
  
  [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
  [{ 'align': [] }], ['link', 'image', 'video'],

  ['clean','code']                                         // remove formatting button
];

  Quill.prototype.getHtml = function() {
        return this.container.querySelector('.ql-editor').innerHTML;
    };

  var quill_" . $this->InstanceName . " = new Quill('#editor_" . $this->InstanceName . "', {
    modules: {
      toolbar: {
           container: toolbarOptions,
           handlers: { image: quill_img_handler, code: quill_html_handler}
                },
       },
    placeholder: '',
    theme: 'snow'
  });
  
  
   $('[name=\"editID\"], [name=\"saveID\"], [type=\"submit\"]').on('click', function() {
      $('#editor_src_" . $this->InstanceName . "').val($('#editor_" . $this->InstanceName ." .ql-editor').html());
   });
   
   function quill_img_handler() {
      $('.elfinder-modal-content').attr('data-option','return=". $this->InstanceName . "');
      $('#elfinderModal').modal('show');
   }
   
  function quill_html_handler() {
      
      $('#editor_" . $this->InstanceName . "').toggleClass('hide');
      $('#editor_src_" . $this->InstanceName . "').toggleClass('hide');
          
      // HTML
      if($('#editor_" . $this->InstanceName . "').hasClass('hide')){
         $('#editor_src_" . $this->InstanceName . "').val($('#editor_" . $this->InstanceName ." .ql-editor').html());
      }
      // EDITOR
      else {
        $('#editor_" . $this->InstanceName . " .ql-editor').html($('#editor_src_" . $this->InstanceName . "').val());
      } 
   }
</script>";

        return $html;
    }

}

?>