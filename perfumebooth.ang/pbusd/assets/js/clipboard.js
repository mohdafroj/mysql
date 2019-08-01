var ClipBoard = {
    firstName: "John",
    lastName : "Doe",
    id       : 5566,
    test : function() {
       return this.firstName + " " + this.lastName;
    }
};

/****
class ClipBoard {
	public ClipBoard(){
		console.log("constructor js");
	}
	public function test(){
		console.log("external js");
	}
}
exports.default = clipBoard;


$('#clipboardButton').click(function(){
  var txt = $('#clipboardContent').val();
  if(!txt || txt == ''){
    return;
  }
  
  copyTextToClipboard(txt);
  $('textarea').val('').focus();
});

function copyTextToClipboard(text) {
  var textArea = document.createElement("textarea");

  textArea.style.position = 'fixed';
  textArea.style.top = 0;
  textArea.style.left = 0;
  textArea.style.width = '2em';
  textArea.style.height = '2em';
  textArea.style.padding = 0;
  textArea.style.border = 'none';
  textArea.style.outline = 'none';
  textArea.style.boxShadow = 'none';
  textArea.style.background = 'transparent';
  textArea.value = text;
  document.body.appendChild(textArea);
  textArea.select();
  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Copying text command was ' + msg);
  } catch (err) {
    console.log('Oops, unable to copy');
  }
  document.body.removeChild(textArea);
}

**/////
