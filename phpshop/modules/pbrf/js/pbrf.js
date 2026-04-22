function print_pbrf() {
    $("#type_send").val("print");
  }
  function pdf_pbrf() {
    $("#type_send").val("pdf");
  }


function DoPrintBig(path) {
    window.open(path, "_blank", "dependent=1,left=0,top=0,width=850,height=650,location=1,menubar=1,resizable=1,scrollbars=1,status=1,titlebar=1,toolbar=1");
}