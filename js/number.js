$('input.number').keyup(function(event) {

  // skip for arrow keys
  if(event.which >= 37 && event.which <= 40) return;

  // format number
  $(this).val(function(index, value) {
    return value
    .replace(/\D/g, "")
    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    ;
  });
});

$('input.number').click(function(event) {
	$(this).select();
});
			
   
var inputs = document.getElementsByTagName('button');
for(var i = 0; i < inputs.length; i++) {
    if(inputs[i].type == 'submit' && inputs[i].value != "GOOD") {
        if(inputs[i].innerHTML == "OK"||inputs[i].innerHTML == "Cancel"||inputs[i].innerHTML == "Now"|| inputs[i].innerHTML == "Clear"){
            //alert(inputs[i].innerHTML);                    
        }else if(inputs[i].innerHTML == '<i class="fa fa-fw fa-times"></i>' || inputs[i].innerHTML == ' &nbsp; Add More &nbsp; ' ){
            inputs[i].setAttribute("onclick", "linkLoading();");
        }else if(inputs[i].innerHTML == '<i class="fa fa-fw fa-save"></i> Post'){
            inputs[i].innerHTML = '<i class="fa fa-fw fa-save"></i> Submit';
            //inputs[i].setAttribute("onclick", "return submitButtonDisabling();");
        }else{                    
            //inputs[i].setAttribute("onclick", "return submitButtonDisabling();");
        }
    }else{
        //inputs[i].setAttribute("onclick", "return linkLoading();");
    }
} 

function submitButtonDisabling(event){
    //alert(this.innerHTML); 
    var t = true;
    this.onclick = function (){
        return t = confirm("Do you really want to Submit?");
    };
    
    var ins = document.getElementsByTagName('button');
    for(var i = 0; i < ins.length; i++) {
        if(ins[i].type.toLowerCase() == 'submit') {
            if(ins[i].innerHTML == 'Sending...'){
                alert("Already Clicked. Please Wait...");
                return false;
            }else{                        
                ins[i].innerHTML = 'Sending...';
            }
        }
    } 
    if(t){
        if(document.getElementById("wait").style.display=="none"){
            //document.getElementById("wait").style.display = "block";
        }
    }
}

/*
var lis = document.getElementsByTagName('a');
for(var i = 0; i < lis.length; i++) {  
    if(lis[i].href != "#top" && lis[i].href != "javascript:void(0);"){                 
        lis[i].setAttribute("onclick", "return linkLoading();");
    } 
}*/

function linkLoading(event){
  if(document.getElementById("wait").style.display=="none"){
        //document.getElementById("wait").style.display = "block";
    }
}


window.unload = function(){ document.getElementById("wait").style.display = "block" }

function onReady(callback) {
  var intervalId = window.setInterval(function() {
    if (document.getElementsByTagName('body')[0] !== undefined) {
      window.clearInterval(intervalId);
      callback.call(this);
    }
  }, 100);
}

onReady(function() {
    document.getElementById("wait").style.display = "none";
});