var validation = {
  isNumberKey: function(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) )
      return false;
    return true;
  },
  isAplhaNum: function(e) {
    if(event.shiftKey){
      if(90>=event.keyCode && event.keyCode>=65){ //you can press shiftkey when input alpha.
        event.returnValue=true;
      }else{
        event.returnValue = false;
      }
    }else{
      if((57>=event.keyCode && event.keyCode>=48) || (105>=event.keyCode && event.keyCode>=96) || (90>=event.keyCode && event.keyCode>=65) || (40>=event.keyCode && event.keyCode>=37) || (event.keyCode == 8) || (event.keyCode == 46) || (event.keyCode == 13) || (event.keyCode == 32)){
        event.returnValue=true;
      }else{
        event.returnValue = false;
      }
    }
  },
  validateEmail: function(val){
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!filter.test(val) ){
      return false;
    }
    return true;
  }
}
